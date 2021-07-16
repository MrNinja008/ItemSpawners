<?php

namespace OguzhanUmutlu\ItemSpawners\async;

use OguzhanUmutlu\ItemSpawners\ItemSpawners;
use OguzhanUmutlu\ItemSpawners\spawners\Spawner;
use pocketmine\level\format\Chunk;
use pocketmine\level\Level;
use pocketmine\math\Vector3;
use pocketmine\scheduler\AsyncTask;
use pocketmine\Server;

class SearchWrongSpawners extends AsyncTask {
    public $chunk;
    public $chunkX;
    public $chunkZ;
    public $spawners;
    public function __construct(string $chunk, int $chunkX, int $chunkZ, array $spawners) {
        $this->chunk = Chunk::fastDeserialize($chunk);
        $this->chunkX = $chunkX;
        $this->chunkZ = $chunkZ;
        $this->spawners = $spawners;
    }

    public function onRun() {
        $chunk = $this->chunk;
        $result = [];
        foreach($this->spawners as $key => $blockIds) {
            $arr = explode(".", $key);
            $x = (int)$arr[0];
            $y = (int)$arr[1];
            $z = (int)$arr[2];
            $chunkX = $x >> 4;
            $chunkZ = $z >> 4;
            if($chunkX == $this->chunkX && $chunkZ == $this->chunkZ) {
                $x = $x & 0x0f;
                $z = $z & 0x0f;
                if($blockIds[1] == $chunk->getBlockId($x, $y, $z)) {
                    $result[] = $key;
                }
            }
        }
        $this->setResult($result);
    }

    public function onCompletion(Server $server) {
        $result = $this->getResult();
        if(!is_array($result)) return;
        foreach($result as $key) {
            $spawner = ItemSpawners::getSpawner($key);
            if($spawner instanceof Spawner) {
                $exp = explode(".", $key);
                $levelId = $exp[3];
                $level = $server->getLevel($levelId);
                if($level instanceof Level)
                    if($level->getBlockIdAt((int)$exp[0], (int)$exp[1], (int)$exp[2]) == $spawner->getChangeBlock()->getId())
                        $level->setBlock(new Vector3((int)$exp[0], (int)$exp[1], (int)$exp[2]), $spawner->getRealBlock());
            }
        }
    }
}