<?php

namespace OguzhanUmutlu\ItemSpawners\spawners\types;

use OguzhanUmutlu\ItemSpawners\spawners\ConfigSpawner;
use pocketmine\block\Block;
use pocketmine\item\Item;
use pocketmine\level\Position;

class CoalSpawner extends ConfigSpawner {
    public $name = "coal";

    public function __construct(Position $position) {
        parent::__construct($position);
        $this->drop = Item::get(Item::COAL);
    }

    public function getType(): string {
        return "CoalSpawner";
    }

    public function getRealBlock(): Block {
        return Block::get(Block::COAL_ORE);
    }

    public function getChangeBlock(): Block {
        return Block::get(Block::COAL_BLOCK);
    }
}