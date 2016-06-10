<?php
#  _    _ _    _  _____ 
# | |  | | |  | |/ ____|
# | |  | | |__| | |     
# | |  | |  __  | |     
# | |__| | |  | | |____ 
#  \____/|_|  |_|\_____|
# The most customisable UHC plugin for Minecraft PE !
namespace Ad5001\UHC\scenario; 
use pocketmine\command\CommandExecutor;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\command\PluginIdentifiableCommand;
use pocketmine\Server;
use pocketmine\utils\Config;
use Ad5001\UHC\scenario\ScenarioInt;

abstract class Scenario implements ScenarioInt {
    
    private $server;
    
    private $name;
    
    private $config;
    
    public function onEnable() {}
    
    
    
    
    public function onStop() {}
    
    
    
    
    public function getServer() {
        return Server::getInstance();
    }
    
    
    
    public function getConfig() {
        return Main::getConfig()->get("Scenarios")[$this->name];
    }
    
    
    
    public function reloadConfig() {
        Main::reloadConfig();
        return Main::getConfig()->get("Scenarios")[$this->name];
    }
    
    
    
    public function saveConfig($cfg) {
        $scenarios = Main::getConfig()->get("Scenarios");
        $scenarios[$this->name] = $cfg;
        Main::getConfig()->set("Scenarios", $scenarios);
        return Main::getConfig->save();
    }
    
    
    
    public function getScenarioFolder() {
        return realPath(Main::getDataFolder . "scenarios/");
    }
}