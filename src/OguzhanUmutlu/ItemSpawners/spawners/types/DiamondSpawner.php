<?php

namespace OguzhanUmutlu\ItemSpawners\spawners\types;

use OguzhanUmutlu\ItemSpawners\spawners\ConfigSpawner;
use pocketmine\block\Block;
use pocketmine\item\Item;
use pocketmine\level\Position;

class DiamondSpawner extends ConfigSpawner {
    public $name = "diamond";

    public function __construct(Position $position) {
        parent::__construct($position);
        $this->drop = Item::get(Item::DIAMOND);
    }

    public function getType(): string {
        return "DiamondSpawner";
    }

    public function getRealBlock(): Block {
        return Block::get(Block::DIAMOND_ORE);
    }

    public function getChangeBlock(): Block {
        return Block::get(Block::DIAMOND_BLOCK);
    }
}