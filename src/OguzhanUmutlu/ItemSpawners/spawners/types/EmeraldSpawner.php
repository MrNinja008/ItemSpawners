<?php

namespace OguzhanUmutlu\ItemSpawners\spawners\types;

use OguzhanUmutlu\ItemSpawners\spawners\ConfigSpawner;
use pocketmine\block\Block;
use pocketmine\item\Item;
use pocketmine\level\Position;

class EmeraldSpawner extends ConfigSpawner {
    public $name = "emerald";

    public function __construct(Position $position) {
        parent::__construct($position);
        $this->drop = Item::get(Item::EMERALD);
    }

    public function getType(): string {
        return "EmeraldSpawner";
    }

    public function getRealBlock(): Block {
        return Block::get(Block::EMERALD_ORE);
    }

    public function getChangeBlock(): Block {
        return Block::get(Block::EMERALD_BLOCK);
    }
}