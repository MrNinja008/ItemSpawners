<?php

namespace Author\Plugin\Spawners;

use OguzhanUmutlu\ItemSpawners\costs\Cost;
use pocketmine\item\Item;
use pocketmine\Player;

class ExampleCustomCost extends Cost {
    public function __construct(int $customCost) {
        parent::__construct($customCost);
    }

    public function getType(): string {
        return "StoneItem";
    }

    public function toString(): string {
        return $this->cost." Stone";
    }

    public function execute(Player $player): bool {
        // check if player has stone
        if(!$player->getInventory()->contains(Item::get(Item::STONE, 0, $this->cost))) return false;
        // remove stone
        $player->getInventory()->removeItem(Item::get(Item::STONE, 0, $this->cost));
        return true;
    }
}