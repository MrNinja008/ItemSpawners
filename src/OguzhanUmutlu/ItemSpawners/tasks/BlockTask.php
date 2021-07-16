<?php

namespace OguzhanUmutlu\ItemSpawners\tasks;

use pocketmine\scheduler\Task;

class BlockTask extends Task {
    public $a;
    public $b;
    public function __construct($a, $b) {
        $this->a=$a;
        $this->b=$b;
    }
    public function onRun(int $currentTick) {
        $this->a->level->setBlock($this->a, $this->b);
    }
}