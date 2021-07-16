<?php

namespace OguzhanUmutlu\ItemSpawners\spawners\types;

use OguzhanUmutlu\ItemSpawners\spawners\ConfigSpawner;
use pocketmine\block\Block;
use pocketmine\item\Item;
use pocketmine\level\Position;

class GoldSpawner extends ConfigSpawner {
    public $name = "gold";

    public function __construct(Position $position) {
        parent::__construct($position);
        $this->drop = Item::get(Item::GOLD_ORE);
    }

    public function getType(): string {
        return "GoldSpawner";
    }

    public function getRealBlock(): Block {
        return Block::get(Block::GOLD_ORE);
    }

    public function getChangeBlock(): Block {
        return Block::get(Block::GOLD_BLOCK);
    }
}