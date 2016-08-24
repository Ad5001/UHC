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


    public function onJoin(\pocketmine\Player $player) {
        if(file_exists($this->getScenariosFolder() . "LobbysBackups/" . $this->getLevel()->getLevel()->getName() . ".json")) {
            $bs = json_decode(file_get_contents($this->getScenariosFolder() . "LobbysBackups/" . $this->getLevel()->getLevel()->getName() . ".json"), true);
            $i = 0;
            $ss = $this->getLevel()->getLevel()->getSafeSpawn();
            for($x = $ss->x + 10; $x >= $ss->x - 10;$x--) {
                for($y = $ss->y + 10; $y >= $ss->y - 10;$y--) {
                    for($z = $ss->z + 10; $z >= $ss->z - 10;$z--) {
                        $this->getLevel()->getLevel()->setBlock(new \pocketmine\math\Vector3($x, $y, $z), new \pocketmine\block\Block(Item::fromString($bs[$i])));
                        $i++;
                    }
                }
            }
        }
    }


    public function onStop(\pocketmine\Player $player) {
        $this->getLogger()->info("Game in level " . $this->getLevel()->getName() . " ended. Reseting world....");
        $this->name = $this->getLevel()->getName();
        $this->ss = $this->getLevel()->getLevel()->getSafeSpawn();
        foreach($this->getLevel()->getLevel()->getPlayers() as $p) {
            $player->teleport($this->getServer()->getLevelByName($this->getMain()->getConfig()->get("LobbyWorld"))->getSafeSpawn());
        }
        $this->winner = $player;
        $h = $this->getServer()->getScheduler()->scheduleRepeatingTask($t = new UHCWorldReseterFetchGenerateTask($this), 10);
        $t->setHandler($h);
    }



    public function delDir(string $path) {
        foreach(array_diff(scandir($path),[".", ".."]) as $p) {
            if(is_dir($path . "/" . $p)) {
                $this->delDir($path . "/" . $p);
            } else {
                unlink($path . "/" . $p);
            }
        }
        rmdir($path);
    }
}



class UHCWorldReseterFetchGenerateTask extends \pocketmine\scheduler\PluginTask{


    public function __construct($main){
        parent::__construct($main->getMain());
        $this->main =$main;
    }

    public function onRun($tick) {
        if($this->main->getServer()->getLevelByName($this->main->name) !== null) {
            $this->main->getLevel()->getLevel()->unload();
            $this->main->delDir($this->main->getServer()->getFilePath() . "worlds/" . $this->name);
            $this->main->getServer()->generateLevel($this->name, intval(sha1(rand(0, 999999))), "pocketmine\\level\\generator\\normal\\Normal");
            $this->main->getLogger()->info("Level " . $this->main->name . " reseted !");
            $this->main->getServer()->getScheduler()->cancelTask($this->getTaskId());
        }
    }
}