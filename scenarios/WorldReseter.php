<?php
USE pocketmine\item\Item;

class UHCWorldReseter extends \Ad5001\UHC\scenario\Scenario {

    public function onPlayerChat(\pocketmine\event\player\PlayerChatEvent $event) {
        if(strpos($event->getMessage(), "/savelobby")) {
                $bs = [];
                @mkdir($this->getScenariosFolder() . "LobbysBackups");
                for($x = $this->getLevel()->getLevel()->getSafeSpawn()->x + 10; $x >= $this->getLevel()->getLevel()->getSafeSpawn()->x - 10;$x--) {
                    for($y = $this->getLevel()->getLevel()->getSafeSpawn()->y + 10; $y >= $this->getLevel()->getLevel()->getSafeSpawn()->y - 10;$y--) {
                        for($z = $this->getLevel()->getLevel()->getSafeSpawn()->z + 10; $z >= $this->getLevel()->getLevel()->getSafeSpawn()->z - 10;$z--) {
                            $b = $this->getLevel()->getLevel()->getBlock(new \pocketmine\math\Vector3($x, $y, $z));
                            array_push($bs, $b->getId() . ":" . $b->getDamage());
                        }
                    }
                }
                file_put_contents($this->getScenariosFolder() . "LobbysBackups/" .$this->getLevel()->getName() . ".json", json_encode(["radius" => 10, "blocks" => $bs]));
                $event->getPlayer()->sendMessage("Lobby saved in a radius of 10 of the spawn");
        }
    }


    public function onStop() {
        $this->getLevel()->getLevel()->unload();
        $this->delDir($this->getServer()->getFilePath() . "worlds/" . $this->getLevel()->getLevel()->getName());
        $this->getServer()->generateLevel($this->getLevel()->getName(), intval(sha1(rand(0, 999999))), "pocketmine\\level\\generator\\normal\\Normal");
        $this->getServer()->loadLevel($this->getLevel()->getName());
        if(file_exists($this->getScenariosFolder() . "LobbysBackups/" .$this->getWorld()->getName() . ".json")) {
            $bs = json_decode(file_get_contents($this->getScenariosFolder() . "LobbysBackups/" .$this->getWorld()->getName() . ".json"), true);
            $i = 0;
            for($x = $this->getLevel()->getLevel()->getSafeSpawn()->x + 10; $x >= $this->getLevel()->getLevel()->getSafeSpawn()->x - 10;$x++) {
                for($y = $this->getLevel()->getLevel()->getSafeSpawn()->y + 10; $y >= $this->getLevel()->getLevel()->getSafeSpawn()->y - 10;$y++) {
                    for($z = $this->getLevel()->getLevel()->getSafeSpawn()->z + 10; $z >= $this->getLevel()->getLevel()->getSafeSpawn()->z - 10;$z++) {
                        $this->getWorld()->getLevel()->setBlock(new \pocketmine\math\Vector3($x, $y, $z), new \pocketmine\block\Block(Item::fromString($bs[$i])));
                        $i++;
                    }
                }
            }
        }
    }



    public function delDir(string $path) {
        foreach(array_diff(scandir($path),[".", ".."]) as $p) {
            if(is_dir($path . "/" . $p)) {
                if(count(array_diff(scandir($path . "/". $p),[".", ".."])) == 0) {
                    rmdir($path . "/" . $p);
                } else {
                    $this->delDir($path . "/" . $p);
                }
            } else {
                unlink($path . "/" . $p);
            }
        }
    }
}