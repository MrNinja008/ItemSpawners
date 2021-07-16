<?php

namespace OguzhanUmutlu\ItemSpawners\events\types;

use OguzhanUmutlu\ItemSpawners\events\SpawnerEvent;
use OguzhanUmutlu\ItemSpawners\spawners\Spawner;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\Cancellable;

class SpawnerPlaceEvent extends SpawnerEvent implements Cancellable {
    private $placeEvent;
    public function __construct(Spawner $spawner, BlockPlaceEvent $event) {
        parent::__construct($spawner);
        $this->placeEvent = $event;
    }

    /*** @return BlockPlaceEvent */
    public function getPlaceEvent(): BlockPlaceEvent {
        return $this->placeEvent;
    }
}