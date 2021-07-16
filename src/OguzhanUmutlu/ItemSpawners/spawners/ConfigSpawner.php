<?php

namespace OguzhanUmutlu\ItemSpawners\spawners;

use OguzhanUmutlu\ItemSpawners\costs\types\CostExperience;
use OguzhanUmutlu\ItemSpawners\costs\types\CostExperienceLevel;
use OguzhanUmutlu\ItemSpawners\costs\types\CostMoney;
use OguzhanUmutlu\ItemSpawners\ItemSpawners;
use pocketmine\item\Item;

abstract class ConfigSpawner extends Spawner {
    /*** @var Item */
    public $drop;
    public $name = "";
    public function getItem(): Item {
        return Item::get($this->drop->getId(), $this->drop->getDamage(), (int)ItemSpawners::$instance->getConfig()->getNested("default-spawners.".$this->name.".levels")[$this->getLevel()-1]["item-count"]);
    }

    public function getTicks(): int {
        return (int)ItemSpawners::$instance->getConfig()->getNested("default-spawners.".$this->name.".cooldown");
    }

    public function getLevelUpData(): array {
        return array_map(function($cost) {
            return [
                "money" => new CostMoney($cost["cost"]),
                "xp" => new CostExperience($cost["cost"]),
                "level" => new CostExperienceLevel($cost["cost"])
            ][$cost["type"]];
        }, array_slice(ItemSpawners::$instance->getConfig()->getNested("default-spawners.".$this->name), 1));
    }
}