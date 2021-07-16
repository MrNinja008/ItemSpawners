<?php

namespace OguzhanUmutlu\ItemSpawners\events\types;

use OguzhanUmutlu\ItemSpawners\events\SpawnerEvent;
use OguzhanUmutlu\ItemSpawners\spawners\Spawner;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\Cancellable;

class SpawnerBreakEvent extends SpawnerEvent implements Cancellable {
    private $breakEvent;
    public function __construct(Spawner $spawner, BlockBreakEvent $event) {
        parent::__construct($spawner);
        $this->breakEvent = $event;
    }

    /*** @return BlockBreakEvent */
    public function getBreakEvent(): BlockBreakEvent {
        return $this->breakEvent;
    }
}