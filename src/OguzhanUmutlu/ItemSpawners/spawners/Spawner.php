<?php

namespace OguzhanUmutlu\ItemSpawners\spawners;

use OguzhanUmutlu\ItemSpawners\tasks\BlockTask;
use OguzhanUmutlu\ItemSpawners\costs\Cost;
use OguzhanUmutlu\ItemSpawners\ItemSpawners;
use pocketmine\block\Block;
use pocketmine\item\Item;
use pocketmine\level\Level;
use pocketmine\level\Position;
use pocketmine\math\Vector3;
use pocketmine\scheduler\Task;
use pocketmine\Server;

abstract class Spawner {
    /*** @var Position */
    private $position;
    private $level = 1;
    private $isClosed = false;
    private $status;
    public const STATUS_STILL = 0;
    public const STATUS_WAITING = 1;
    public function __construct(Position $position) {
        $this->position = $position;
        $this->status = self::STATUS_STILL;
    }

    /*** @return string */
    abstract public function getType(): string;

    /*** @return Item */
    abstract public function getItem(): Item;

    /*** @return int */
    public function getLevel(): int {
        return $this->level;
    }

    /*** @param int $level */
    public function setLevel(int $level): void {
        $this->level = $level;
        $f = $this->position->floor();
        $key = $f->x.".".$f->y.".".$f->z.".".$this->position->level->getId();
        if(ItemSpawners::getSpawner($key))
            ItemSpawners::setSpawner($key, $this);
    }

    public function addLevel(): void {
        $this->setLevel($this->level+1);
    }

    /*** @return int */
    abstract public function getTicks(): int;

    /*** @return Position */
    public function getPosition(): Position {
        return $this->position;
    }

    /*** @return Block */
    abstract public function getRealBlock(): Block;

    /*** @return Block */
    abstract public function getChangeBlock(): Block;

    /*** @return bool */
    public function isClosed(): bool {
        return $this->isClosed;
    }

    /*** @return int */
    public function getStatus(): int {
        return $this->status;
    }

    /*** @param int $status */
    public function setStatus(int $status): void {
        $this->status = $status;
    }

    /*** @return Cost[] */
    abstract public function getLevelUpData(): array;

    public function nextLevelCost(): ?Cost {
        if(count($this->getLevelUpData()) <= $this->getLevel()) return null;
        return $this->getLevelUpData()[$this->getLevel()+1];
    }

    public function spawn(): void {
        $this->isClosed = false;
        ItemSpawners::$instance->getScheduler()->scheduleDelayedTask(new BlockTask($this->position, $this->getRealBlock()), 1);
    }

    public function break(): void {
        $this->setStatus(self::STATUS_WAITING);
        if($this->getTicks() > 1)
            ItemSpawners::$instance->getScheduler()->scheduleDelayedTask(new BlockTask($this->position, $this->getChangeBlock()), 1);
        ItemSpawners::$instance->getScheduler()->scheduleDelayedTask(new class($this) extends Task {
            public $spawner;
            public function __construct(Spawner $spawner) {
                $this->spawner = $spawner;
            }
            public function onRun(int $currentTick) {
                $spawner = $this->spawner;
                if($spawner->isClosed())
                    return;
                $spawner->getPosition()->level->setBlock($spawner->getPosition(), $spawner->getRealBlock());
                $spawner->setStatus(Spawner::STATUS_STILL);
            }
        }, $this->getTicks());
    }

    public function kill(): void {
        $this->isClosed = true;
    }

    public function toArray(): array {
        return [
            "type" => $this->getType(),
            "position" => [$this->position->x, $this->position->y, $this->position->z, $this->position->level->getFolderName()],
            "level" => $this->level
        ];
    }

    public static function fromArray(array $array): ?Spawner {
        $class = ItemSpawners::getSpawnerTypes()[$array["type"]] ?? "";
        if(!class_exists($class)) return null;
        if(!Server::getInstance()->isLevelLoaded($array["position"][3]))
            Server::getInstance()->loadLevel($array["position"][3]);
        $level = Server::getInstance()->getLevelByName($array["position"][3]);
        if(!$level instanceof Level) return null;
        $vector3 = new Vector3((int)$array["position"][0], (int)$array["position"][1], (int)$array["position"][2]);
        $spawner = new $class($level->getBlock($vector3));
        if(!$spawner instanceof Spawner) return null;
        $spawner->level = $array["level"];
        return $spawner;
    }
}
