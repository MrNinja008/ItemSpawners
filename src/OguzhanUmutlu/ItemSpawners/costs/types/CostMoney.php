<?php

namespace OguzhanUmutlu\ItemSpawners\costs\types;

use OguzhanUmutlu\ItemSpawners\costs\Cost;
use onebone\economyapi\EconomyAPI;
use pocketmine\Player;

class CostMoney extends Cost {
    public function __construct(float $money) {
        parent::__construct($money);
    }

    public function getType(): string {
        return "Money";
    }

    public function toString(): string {
        return $this->cost . (class_exists(EconomyAPI::class) ? EconomyAPI::getInstance()->getMonetaryUnit() : "Unknown");
    }

    public function execute(Player $player): bool {
        if(!class_exists(EconomyAPI::class)) return false;
        if(EconomyAPI::getInstance()->myMoney($player) < $this->cost) return false;
        EconomyAPI::getInstance()->reduceMoney($player, $this->cost);
        return true;
    }
}