<?php
#  _    _ _    _  _____ 
# | |  | | |  | |/ ____|
# | |  | | |__| | |     
# | |  | |  __  | |     
# | |__| | |  | | |____ 
#  \____/|_|  |_|\_____|
# The most customisable UHC plugin for Minecraft PE !
namespace Ad5001\UHC\task; 
use pocketmine\command\CommandSender;
use pocketmine\command\Command;
use pocketmine\event\Listener;
use pocketmine\Server;
use pocketmine\level\Level;
use pocketmine\plugin\Plugin;
use pocketmine\utils\TextFormat as C;
use pocketmine\scheduler\PluginTask;
use pocketmine\Player;

use Ad5001\UHC\UHCWorld;
use Ad5001\UHC\UHCGame;
use Ad5001\UHC\Main;

class StartGameTask extends PluginTask {
    public function __construct(Plugin $main, UHCWorld $world) {
        parent::__construct($main);
        $this->main = $main;
        $this->world = $world;
        $this->seconds = 30;
        $this->close = false;
    }
    
    
    public function close() {
        $this->close = true;
    }
    
    
    public function onRun($tick) {
        if(!$this->close) {
        $this->main->getLogger()->debug($this->seconds);
        switch($this->seconds) {
            case 30:
            foreach($this->world->getLevel()->getPlayers() as $player) {
                $player->sendMessage(Main::PREFIX . C::YELLOW . "30 seconds before the game starts");
            }
            break;
            case 20:
            foreach($this->world->getLevel()->getPlayers() as $player) {
                $player->sendMessage(Main::PREFIX . C::YELLOW . "20 seconds before the game starts");
            }
            break;
            case 15:
            foreach($this->world->getLevel()->getPlayers() as $player) {
                $player->sendMessage(Main::PREFIX . C::YELLOW . "15 seconds before the game starts");
            }
            break;
            case 10:
            foreach($this->world->getLevel()->getPlayers() as $player) {
                $player->sendMessage(Main::PREFIX . C::YELLOW . "10 seconds before the game starts");
            }
            break;
            case 5:
            foreach($this->world->getLevel()->getPlayers() as $player) {
                $player->sendMessage(Main::PREFIX . C::YELLOW . "5 seconds before the game starts");
            }
            break;
            case 4:
            foreach($this->world->getLevel()->getPlayers() as $player) {
                $player->sendMessage(Main::PREFIX . C::YELLOW . "4 seconds before the game starts");
            }
            break;
            case 3:
            foreach($this->world->getLevel()->getPlayers() as $player) {
                $player->sendMessage(Main::PREFIX . C::YELLOW . "3 seconds before the game starts");
            }
            break;
            case 2:
            foreach($this->world->getLevel()->getPlayers() as $player) {
                $player->sendMessage(Main::PREFIX . C::YELLOW . "2 seconds before the game starts");
            }
            break;
            case 1:
            foreach($this->world->getLevel()->getPlayers() as $player) {
                $player->sendMessage(Main::PREFIX . C::YELLOW . "1 second before the game starts");
            }
            break;
            case 0:
            $this->main->games[$this->world->getLevel()->getName()] = new UHCGame($this->main, $this->world);
            $this->close();
            $this->seconds = -1;
            break;
        }
        $this->seconds--;
    }
    }
}
