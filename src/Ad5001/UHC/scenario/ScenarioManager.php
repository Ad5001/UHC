<?php
namespace Ad5001\UHC\scenario;
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
use Ad5001\UHC\Main;
use Ad5001\UHC\UHCWorld;
use Ad5001\UHC\UHCGame;



class ScenarioManager {


    protected $main;
    protected $server;
    protected $scenarios;
    protected $level;
    protected $usedscenarios;


   public function __construct(Main $main, UHCWorld $game) {
        $this->main = $main;
        $this->server = $main->getServer();
        $files = array_diff(scandir($main->getDataFolder() . "scenarios"), [".", ".."]);
        $this->scenarios = [];
        $this->level = $game;
        $this->usedscenarios = [];
        foreach ($files as $file) {
            if(!is_dir($this->main->getDataFolder() . "scenarios/" . $file)) {
                require($this->main->getDataFolder() . "scenarios/" . $file);
                $classn = $this->main->getClasses(file_get_contents($this->main->getDataFolder() . "scenarios/" . $file));
                $this->scenarios[explode("\\", $classn)[count(explode("\\", $classn)) - 1]] = $classn;
                @mkdir($this->main->getDataFolder() . "scenarios/" . explode("\\", $classn)[count(explode("\\", $classn)) - 1]);
            }
        }
        if(isset($this->main->getConfig()->get($game->getLevel()->getName())["default_scenarios"])) {
            if(is_array($this->main->getConfig()->get($game->getLevel()->getName())["default_scenarios"])){
                foreach($this->main->getConfig()->get($game->getLevel()->getName())["default_scenarios"] as $sc) {
                    $this->addScenario($sc);
                }
            }
        }
    }



    public function addScenario(string $name) {
        if(!isset($this->usedscenarios[$name]) and !$this->level->isStarted()) {
            $this->usedscenarios[$name] = new $name($this->server, $this->level);
            return true;
        }
        return false;
    }



    public function rmScenario(string $name) {
        if(isset($this->usedscenarios[$name]) and !$this->level->isStarted()) {
            unset($this->usedscenarios[$name]);
            $this->levels[$level->getName()]->onGameStop();
            return true;
        }
        return false;
    }


    public function getLevel() {
        return $this->level;
    }


    public function getScenarios() {
        return $this->scenarios;
    }


    public function getUsedScenarios() {
        return $this->usedscenarios;
    }



}