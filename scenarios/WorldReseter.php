<?php
USE pocketmine\item\Item;

class UHCWorldReseter extends \Ad5001\UHC\scenario\Scenario{

    public function onPlayerCommand(\pocketmine\event\player\PlayerCommandPreprocessEvent $event) {
        $args = explode($event->getMessage());
        if($args[0] == "/savelobby"){
            $bs = [];
            if(isset($args[1])) {
                @mkdir($this->getScenariosFoler() . "LobbysBackups");
                for($x = $this->world->getLevel()->getSafeSpawn()->x + $args[1]; $x >= $this->world->getLevel()->getSafeSpawn()->x - $args[1];$x++) {
                    for($y = $this->world->getLevel()->getSafeSpawn()->y + $args[1]; $y >= $this->world->getLevel()->getSafeSpawn()->y - $args[1];$y++) {
                        for($z = $this->world->getLevel()->getSafeSpawn()->z + $args[1]; $z >= $this->world->getLevel()->getSafeSpawn()->z - $args[1];$z++) {
                            $b = $this->world->getLevel()->getBlock(new \pocketmine\math\Vector3($x, $y, $z));
                            array_push($bs, $b->getId() . ":" . $b->getDamage());
                        }
                    }
                }
                file_put_contents($this->getScenariosFolder() . "LobbysBackups/" .$this->world->getName() . ".json", json_encode(["radius" => $args[1], "blocks" => $bs]));
                $event->getPlayer()->sendMessage("Lobby saved in a radius of $args[1] of the spawn");
                $event->setCancelled();
            }
        }
    }


    public function __construct(\pocketmine\Server $serv, \Ad5001\UHC\UHCWorld $w) {
        parent::__construct($serv,$w);
        if(file_exists($this->getScenariosFolder() . "LobbysBackups/" .$w->getName() . ".json")) {
            $bs = json_decode(file_get_contents($this->getScenariosFolder() . "LobbysBackups/" .$w->getName() . ".json"), true);
            $i = 0;
            for($x = $this->world->getLevel()->getSafeSpawn()->x + $args[1]; $x >= $this->world->getLevel()->getSafeSpawn()->x - $args[1];$x++) {
                for($y = $this->world->getLevel()->getSafeSpawn()->y + $args[1]; $y >= $this->world->getLevel()->getSafeSpawn()->y - $args[1];$y++) {
                    for($z = $this->world->getLevel()->getSafeSpawn()->z + $args[1]; $z >= $this->world->getLevel()->getSafeSpawn()->z - $args[1];$z++) {
                        $w->getLevel()->setBlock(new \pocketmine\math\Vector3($x, $y, $z), new \pocketmine\block\Block(Item::fromString($bs[$i])));
                        $i++;
                    }
                }
            }
        }
    }
}