<?php

namespace OguzhanUmutlu\ItemSpawners\costs\types;

use OguzhanUmutlu\ItemSpawners\costs\Cost;
use pocketmine\Player;

class CostExperienceLevel extends Cost {
    public function __construct(int $experienceLevel) {
        parent::__construct($experienceLevel);
    }

    public function getType(): string {
        return "ExperienceLevel";
    }

    public function toString(): string {
        return $this->cost . " XP Level";
    }

    public function execute(Player $player): bool {
        if($player->getXpLevel() < $this->cost) return false;
        $player->subtractXpLevels($this->cost);
        return true;
    }
}