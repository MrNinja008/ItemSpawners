<?php

namespace OguzhanUmutlu\ItemSpawners\spawners\types;

use OguzhanUmutlu\ItemSpawners\spawners\ConfigSpawner;
use pocketmine\block\Block;
use pocketmine\item\Item;
use pocketmine\level\Position;

class LapisLazuliSpawner extends ConfigSpawner {
    public $name = "lapislazuli";

    public function __construct(Position $position) {
        parent::__construct($position);
        $this->drop = Item::get(Item::DYE, 4);
    }

    public function getType(): string {
        return "LapisSpawner";
    }

    public function getRealBlock(): Block {
        return Block::get(Block::LAPIS_ORE);
    }

    public function getChangeBlock(): Block {
        return Block::get(Block::LAPIS_BLOCK);
    }
}