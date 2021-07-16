<?php

namespace OguzhanUmutlu\ItemSpawners\spawners\types;

use OguzhanUmutlu\ItemSpawners\spawners\ConfigSpawner;
use pocketmine\block\Block;
use pocketmine\item\Item;
use pocketmine\level\Position;

class IronSpawner extends ConfigSpawner {
    public $name = "iron";

    public function __construct(Position $position) {
        parent::__construct($position);
        $this->drop = Item::get(Item::IRON_ORE);
    }

    public function getType(): string {
        return "IronSpawner";
    }

    public function getRealBlock(): Block {
        return Block::get(Block::IRON_ORE);
    }

    public function getChangeBlock(): Block {
        return Block::get(Block::IRON_BLOCK);
    }
}