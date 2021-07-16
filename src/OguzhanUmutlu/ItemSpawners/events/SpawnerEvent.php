<?php

namespace OguzhanUmutlu\ItemSpawners\events;

use OguzhanUmutlu\ItemSpawners\spawners\Spawner;
use pocketmine\event\Event;

abstract class SpawnerEvent extends Event {
    private $spawner;
    public function __construct(Spawner $spawner) {
        $this->spawner = $spawner;
    }

    /*** @return Spawner */
    public function getSpawner(): Spawner {
        return $this->spawner;
    }
}