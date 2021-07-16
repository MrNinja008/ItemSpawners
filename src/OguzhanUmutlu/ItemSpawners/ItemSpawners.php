<?php

namespace OguzhanUmutlu\ItemSpawners;

use BadMethodCallException;
use OguzhanUmutlu\ItemSpawners\costs\Cost;
use OguzhanUmutlu\ItemSpawners\spawners\Spawner;
use OguzhanUmutlu\ItemSpawners\spawners\types\CoalSpawner;
use OguzhanUmutlu\ItemSpawners\spawners\types\DiamondSpawner;
use OguzhanUmutlu\ItemSpawners\spawners\types\EmeraldSpawner;
use OguzhanUmutlu\ItemSpawners\spawners\types\GoldSpawner;
use OguzhanUmutlu\ItemSpawners\spawners\types\IronSpawner;
use OguzhanUmutlu\ItemSpawners\spawners\types\LapisLazuliSpawner;
use OguzhanUmutlu\ItemSpawners\spawners\types\RedstoneSpawner;
use pocketmine\level\Position;
use pocketmine\plugin\PluginBase;
use pocketmine\Server;
use pocketmine\utils\Config;

class ItemSpawners extends PluginBase {
    public const DATA_ID = "ItemSpawnerId";
    public const DATA_LEVEL = "SpawnerLevel";
    /*** @var Spawner[] */
    private static $spawners = [];
    public static $failedSpawners = [];
    private static $savedSpawners = [];
    /*** @var ItemSpawners */
    public static $instance;
    /*** @var Config */
    private $dataConfig;
    /*** @var Config */
    public $langConfig;
    public function onEnable() {
        self::$instance = $this;
        $this->saveDefaultConfig();
        $this->saveResource("data.yml");
        $this->dataConfig = new Config($this->getDataFolder()."data.yml");
        $this->saveResource("lang/".$this->getConfig()->getNested("lang").".yml");
        $this->langConfig = new Config($this->getDataFolder()."lang/".$this->getConfig()->getNested("lang").".yml");
        foreach($this->getDataConfig()->getNested("spawners", []) as $key => $spawner) {
            $converted = Spawner::fromArray($spawner);
            if($converted && !isset(self::$spawners[$key])) {
                $f = $converted->getPosition()->floor();
                $keyA = $f->x.".".$f->y.".".$f->z.".".$converted->getPosition()->level->getId();
                self::$spawners[$keyA] = $converted;
            } else self::$failedSpawners[$key] = $spawner;
        }
        Server::getInstance()->getPluginManager()->registerEvents(new EventListener(), $this);
        $spawners = ItemSpawners::$instance->getConfig()->getNested("default-spawners");
        if($spawners["coal"]["enabled"])
            self::registerSpawner(new CoalSpawner(new Position()));
        if($spawners["iron"]["enabled"])
            self::registerSpawner(new IronSpawner(new Position()));
        if($spawners["gold"]["enabled"])
            self::registerSpawner(new GoldSpawner(new Position()));
        if($spawners["lapislazuli"]["enabled"])
            self::registerSpawner(new LapisLazuliSpawner(new Position()));
        if($spawners["redstone"]["enabled"])
            self::registerSpawner(new RedstoneSpawner(new Position()));
        if($spawners["diamond"]["enabled"])
            self::registerSpawner(new DiamondSpawner(new Position()));
        if($spawners["emerald"]["enabled"])
            self::registerSpawner(new EmeraldSpawner(new Position()));
    }
    public static function registerSpawner(Spawner $spawner): void {
        $non = array_filter($spawner->getLevelUpData(), function($n){return !$n instanceof Cost;});
        if(!empty($non)) throw new BadMethodCallException(get_class($spawner)."::getLevelUpData() expected Cost[], ".implode("|",array_map(function($n){return get_class($n)."[]";},$non))." given.");
        if(isset(self::$savedSpawners[$spawner->getType()])) return;
        self::$savedSpawners[$spawner->getType()] = get_class($spawner);
    }
    public static function unregisterSpawner(string $type): void {
        if(!isset(self::$savedSpawners[$type])) return;
        unset(self::$savedSpawners[$type]);
    }
    public static function getSpawnerTypes(): array {
        return self::$savedSpawners;
    }
    public static function getSpawners(): ?array {
        return self::$spawners;
    }
    public static function getSpawner($a): ?Spawner {
        return self::$spawners[$a] ?? null;
    }
    public static function setSpawner($a, $b): void {
        self::$spawners[$a] = $b;
        self::saveSpawners();
    }
    public static function unsetSpawner($a): void {
        unset(self::$spawners[$a]);
        self::saveSpawners();
    }
    public static function saveSpawners(): void {
        self::$instance->getDataConfig()->setNested("spawners", array_map(function($s){return $s->toArray();},self::$spawners));
        self::$instance->getDataConfig()->save();
        self::$instance->getDataConfig()->reload();
    }
    public function getDataConfig(): Config {
        return $this->dataConfig;
    }
}