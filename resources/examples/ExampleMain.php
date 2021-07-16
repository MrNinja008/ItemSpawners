<?php

namespace Author\Plugin\Spawners;

use OguzhanUmutlu\ItemSpawners\ItemSpawners;
use pocketmine\level\Position;
use pocketmine\plugin\PluginBase;

class ExampleMain extends PluginBase {
    public function onEnable() {
        ItemSpawners::registerSpawner(new ExampleSpawner(new Position()));
    }
}