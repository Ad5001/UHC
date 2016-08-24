<?php
namespace Ad5001\UHC;
#  _    _ _    _  _____ 
# | |  | | |  | |/ ____|
# | |  | | |__| | |     
# | |  | |  __  | |     
# | |__| | |  | | |____ 
#  \____/|_|  |_|\_____|
# The most customisable UHC plugin for Minecraft PE !

use pocketmine\Server;
use pocketmine\Player;
use pocketmine\level\Level;


use Ad5001\UHC\task\StartGameTask;



class UHCManager {


    protected $main;
    protected $server;
    protected $games;
    protected $levels;
    protected $startedgames;


   public function __construct(Main $main) {
        $this->main = $main;
        $this->server = $main->getServer();
        $this->games = [];
        $this->levels = [];
        $this->startedgames = [];
    }



    public function startUHC(Level $level) {
        if(isset($this->levels[$level->getName()]) and !isset($this->startedgames[$level->getName()])) {
            $ft = $this->main->getServer()->getScheduler()->scheduleRepeatingTask($t = new StartGameTask($this->main, $this->levels[$level->getName()]), 20);
            $t->setHandler($ft);
            $this->startedgames[$level->getName()] = true;
            foreach($this->levels[$level->getName()]->scenarioManager->getUsedScenarios() as $sc) {
                $sc->onStart();
            }
            return true;
        }
        return false;
    }



    public function stopUHC(Level $level, Player $player) {
        if(isset($this->startedgames[$level->getName()])) {
            foreach($this->levels[$level->getName()]->scenarioManager->getUsedScenarios() as $sc) {
                $sc->onStop($player);
            }
            $started = [];
            foreach($this->startedgames as $name => $game) {
                if($name !== $level->getName()) {
                    $started[$name] = $game;
                }
            }
            $this->startedgames = $started;
            $this->main->getLogger()->info("Game " . $level->getName() . " stoped");
            return true;
        }
        return false;
    }



    public function registerLevel(Level $level) {
        if(!array_key_exists($level->getName(), $this->levels)) {
            $this->levels[$level->getName()] = new UHCWorld($this->main,$level,$this->main->getConfig()->get("worlds")[$level->getName()]["maxplayers"],$this->main->getConfig()->get("worlds")[$level->getName()]["radius"]);
        }
    }


    public function getLevels() {
        return $this->levels;
    }


    public function getStartedUHCs() {
        return $this->startedgames;
    }


    public function addStartedUHC(string $name, UHCGame $game) {
        $this->startedgames[$name] = $game;
        return true;
    }


}