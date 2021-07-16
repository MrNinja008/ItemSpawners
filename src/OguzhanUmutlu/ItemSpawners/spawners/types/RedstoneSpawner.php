<?php

namespace OguzhanUmutlu\ItemSpawners\spawners\types;

use OguzhanUmutlu\ItemSpawners\spawners\ConfigSpawner;
use pocketmine\block\Block;
use pocketmine\item\Item;
use pocketmine\level\Position;

class RedstoneSpawner extends ConfigSpawner {
    public $name = "redstone";

    public function __construct(Position $position) {
        parent::__construct($position);
        $this->drop = Item::get(Item::REDSTONE);
    }

    public function getType(): string {
        return "RedstoneSpawner";
    }

    public function getRealBlock(): Block {
        return Block::get(Block::REDSTONE_ORE);
    }

    public function getChangeBlock(): Block {
        return Block::get(Block::REDSTONE_BLOCK);
    }
}