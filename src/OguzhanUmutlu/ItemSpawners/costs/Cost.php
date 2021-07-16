<?php

namespace OguzhanUmutlu\ItemSpawners\costs;

use pocketmine\Player;

abstract class Cost {
    public $cost;
    public function __construct($cost) {
        $this->cost = $cost;
    }

    abstract public function getType(): string;
    abstract public function toString(): string;
    abstract public function execute(Player $player): bool;
}