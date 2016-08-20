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
            $ft = $this->main->getServer()->getScheduler()->scheduleRepeatingTask($t = new StartGameTask($this, $this->levels[$level->getName()]), 20);
            $t->setHandler($ft);
            $this->startedgames[$level->getName()] = true;
            $this->levels[$level->getName()]->onUHCStart();
            return true;
        }
        return false;
    }



    public function stopUHC(Level $level) {
        if(isset($this->startedgames[$level->getName()])) {
            unset($this->startedgames[$level->getName()]);
            $this->levels[$level->getName()]->onUHCStop();
            return true;
        }
        return false;
    }



    public function registerLevel(Level $level) {
        if(!array_key_exists($level->getName(), $this->levels)) {
            $this->levels[$level->getName()] = new UHCWorld($this->m,$level,$this->main->getConfig()->get("worlds")[$level->getName()]["maxplayers"],$this->main->getConfig()->get("worlds")[$level->getName()]["radius"]);
        } else {
            $this->main->getLogger()->warning("{$level->getName()} is already registered.");
        }
    }


    public function getLevels() {
        return $this->levels;
    }


    public function getStartedUHCs() {
        return $this->startedgames;
    }


}