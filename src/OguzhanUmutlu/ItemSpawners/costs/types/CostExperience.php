<?php

namespace OguzhanUmutlu\ItemSpawners\costs\types;

use OguzhanUmutlu\ItemSpawners\costs\Cost;
use pocketmine\Player;

class CostExperience extends Cost {
    public function __construct(int $xp) {
        parent::__construct($xp);
    }

    public function getType(): string {
        return "Experience";
    }

    public function toString(): string {
        return $this->cost . " XP";
    }

    public function execute(Player $player): bool {
        if($player->getCurrentTotalXp() < $this->cost) return false;
        $player->subtractXp($this->cost);
        return true;
    }
}