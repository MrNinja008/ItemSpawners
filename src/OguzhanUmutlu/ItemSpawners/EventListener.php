<?php

namespace OguzhanUmutlu\ItemSpawners;

use dktapps\pmforms\MenuForm;
use dktapps\pmforms\MenuOption;
use OguzhanUmutlu\ItemSpawners\async\SearchWrongSpawners;
use OguzhanUmutlu\ItemSpawners\events\types\SpawnerBreakEvent;
use OguzhanUmutlu\ItemSpawners\events\types\SpawnerPlaceEvent;
use OguzhanUmutlu\ItemSpawners\spawners\Spawner;
use pocketmine\block\Block;
use pocketmine\block\BlockToolType;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\level\ChunkLoadEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\plugin\PluginEnableEvent;
use pocketmine\item\enchantment\Enchantment;
use pocketmine\item\Item;
use pocketmine\Player;
use pocketmine\scheduler\Task;
use pocketmine\Server;

class EventListener implements Listener {
    public function onPlaceSpawner(BlockPlaceEvent $event) {
        $item = $event->getItem();
        $block = $event->getBlock();
        if(!$item->getNamedTag()->hasTag(ItemSpawners::DATA_ID)) return;
        $f = $block->floor();
        $type = ItemSpawners::getSpawnerTypes()[$item->getNamedTag()->getString(ItemSpawners::DATA_ID)] ?? null;
        $key = $f->x.".".$f->y.".".$f->z.".".$block->level->getId();
        if(ItemSpawners::getSpawner($key) || is_null($type) || !class_exists($type)) {
            $event->setCancelled();
            return;
        }
        ItemSpawners::$instance->getScheduler()->scheduleDelayedTask(new class($event) extends Task {
            public $event;
            public function __construct(BlockPlaceEvent $event) {
                $this->event = $event;
            }
            public function onRun(int $currentTick) {
                $event = $this->event;
                if($event->isCancelled()) return;
                $item = $event->getItem();
                $block = $event->getBlock();
                if(!$block->level) return;
                if(!$item->getNamedTag()->hasTag(ItemSpawners::DATA_ID)) return;
                $f = $block->floor();
                $type = ItemSpawners::getSpawnerTypes()[$item->getNamedTag()->getString(ItemSpawners::DATA_ID)] ?? null;
                $key = $f->x.".".$f->y.".".$f->z.".".$block->level->getId();
                if(ItemSpawners::getSpawner($key) || is_null($type) || !class_exists($type))
                    return;
                $spawner = new $type($block);
                $ev = new SpawnerPlaceEvent($spawner, $event);
                if($ev->isCancelled()) return;
                if(!$spawner instanceof Spawner) return;
                $spawner->setLevel($item->getNamedTag()->getInt(ItemSpawners::DATA_LEVEL, 1));
                $spawner->spawn();
                ItemSpawners::setSpawner($key, $spawner);
            }
        }, 1);

    }
    public function onBreakSpawner(BlockBreakEvent $event) {
        $block = $event->getBlock();
        $f = $block->floor();
        $key = $f->x.".".$f->y.".".$f->z.".".$block->level->getId();
        $spawner = ItemSpawners::getSpawner($key);
        if(!$spawner instanceof Spawner) return;
        if($spawner->getStatus() == Spawner::STATUS_WAITING) {
            $event->setCancelled();
            return;
        }
        $event->setDrops([]);
        ItemSpawners::$instance->getScheduler()->scheduleDelayedTask(new class($event) extends Task {
            public $event;
            public function __construct(BlockBreakEvent $event) {
                $this->event = $event;
            }
            public function onRun(int $currentTick) {
                $event = $this->event;
                if($event->isCancelled()) return;
                $block = $event->getBlock();
                if(!$block->level) return;
                $f = $block->floor();
                $key = $f->x.".".$f->y.".".$f->z.".".$block->level->getId();
                $spawner = ItemSpawners::getSpawner($key);
                if(!$spawner instanceof Spawner) return;
                $ev = new SpawnerBreakEvent($spawner, $event);
                if($ev->isCancelled()) return;
                if($event->getItem()->hasEnchantment(Enchantment::SILK_TOUCH)) {
                    $item = Item::get(Item::MONSTER_SPAWNER);
                    $item->getNamedTag()->setString(ItemSpawners::DATA_ID, $spawner->getType());
                    $item->getNamedTag()->setInt(ItemSpawners::DATA_LEVEL, $spawner->getLevel());
                    if($block->isCompatibleWithTool($item) && !$event->getPlayer()->isCreative() && !$event->getPlayer()->isSpectator())
                        $block->level->dropItem($block, $item);
                    $spawner->kill();
                    ItemSpawners::unsetSpawner($key);
                } else {
                    $block->level->dropItem($block, $spawner->getItem());
                    $spawner->break();
                }
            }
        }, 1);
    }
    public function onOpenSpawner(PlayerInteractEvent $event) {
        ItemSpawners::$instance->getScheduler()->scheduleDelayedTask(new class($event) extends Task {
            public $event;
            public function __construct(PlayerInteractEvent $event) {
                $this->event = $event;
            }
            public function onRun(int $currentTick) {
                $event = $this->event;
                if($event->isCancelled()) return;
                if($event->getAction() != PlayerInteractEvent::RIGHT_CLICK_BLOCK) return;
                $block = $event->getBlock();
                if(!$block->level) return;
                $f = $block->floor();
                $key = $f->x.".".$f->y.".".$f->z.".".$block->level->getId();
                $spawner = ItemSpawners::getSpawner($key);
                if(!$spawner instanceof Spawner) return;
                $player = $event->getPlayer();
                $player->sendForm(new MenuForm(
                    T::T("spawner-menu"),
                    T::T("spawner-description", [$spawner->getLevel()]),
                    [
                        new MenuOption(T::T("level-up-button", [($spawner->nextLevelCost() ? T::T("cost", [$spawner->nextLevelCost()->toString()]) : T::T("max"))])),
                        new MenuOption(T::T("remove-button"))
                    ],
                    function(Player $player, int $res) use ($spawner, $block): void {
                        if($spawner->isClosed()) return;
                        switch($res) {
                            case 0:
                                if(!$spawner->nextLevelCost()) {
                                    $player->sendMessage(T::T("maxed"));
                                    return;
                                }
                                if(!$spawner->nextLevelCost()->execute($player)) {
                                    $player->sendMessage(T::T("enough-cost"));
                                    return;
                                }
                                $spawner->addLevel();
                                $player->sendMessage(T::T("leveled-up"));
                                break;
                            case 1:
                                $item = Item::get(Item::MONSTER_SPAWNER);
                                $item->getNamedTag()->setString(ItemSpawners::DATA_ID, $spawner->getType());
                                $item->getNamedTag()->setInt(ItemSpawners::DATA_LEVEL, $spawner->getLevel());
                                if(!$player->getInventory()->canAddItem($item)) {
                                    $player->sendMessage(T::T("enough-space"));
                                    return;
                                }
                                $block->level->setBlock($block, Block::get(Block::AIR));
                                $player->getInventory()->addItem($item);
                                $player->sendMessage(T::T("remove-success"));
                                break;
                        }
                    }
                ));
            }
        }, 1);
    }
    public function onPluginLoad(PluginEnableEvent $event) {
        if($event->getPlugin() instanceof ItemSpawners) return;
        foreach(ItemSpawners::$failedSpawners as $key => $spawner) {
            $converted = Spawner::fromArray($spawner);
            if($converted && !ItemSpawners::getSpawner($key)) {
                $f = $converted->getPosition()->floor();
                $keyA = $f->x.".".$f->y.".".$f->z.".".$converted->getPosition()->level->getId();
                ItemSpawners::setSpawner($keyA, $converted);
                unset(ItemSpawners::$failedSpawners[$keyA]);
            }
        }
    }
    public function onChunkLoad(ChunkLoadEvent $event) {
        $chunk = $event->getChunk();
        $class = new SearchWrongSpawners($chunk->fastSerialize(), $chunk->getX(), $chunk->getZ(), array_map(function($spawner){return [$spawner->getRealBlock()->getId(), $spawner->getChangeBlock()->getId()];},ItemSpawners::getSpawners()));
        Server::getInstance()->getAsyncPool()->submitTask($class);
    }
}