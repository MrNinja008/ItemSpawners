<?php

namespace Author\Plugin\Spawners;

use OguzhanUmutlu\ItemSpawners\spawners\Spawner;
use OguzhanUmutlu\ItemSpawners\costs\types\CostMoney;
use pocketmine\block\Block;
use pocketmine\item\Item;

class ExampleSpawner extends Spawner {
    public function getType(): string {
        return "ExampleSpawner"; // Spawner's type
    }

    public function getItem(): Item {
        return Item::get(Item::FISH); // The drop when someone breaks spawner.
    }

    public function getTicks(): int {
        return 20; // Cool down of spawner
    }

    public function getRealBlock(): Block {
        return Block::get(Block::PLANKS); // Block that players can mine
    }

    public function getChangeBlock(): Block {
        return Block::get(Block::BEACON); // Block that comes when cool down happens
    }

    public function getLevelUpData(): array {
        return [
            new CostMoney(100), // level 1 -> level 2
            new CostMoney(200), // level 2 -> level 3
            new CostMoney(300), // level 3 -> level 4
            new CostMoney(400), // level 4 -> level 5
            new CostMoney(500), // level 5 -> level 6
            new ExampleCustomCost(1) // level 6 -> level 7 // custom cost mentioned in ExampleCustomCost
        ];
    }
}