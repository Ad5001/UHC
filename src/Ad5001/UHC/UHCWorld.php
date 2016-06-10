<?php
#  _    _ _    _  _____ 
# | |  | | |  | |/ ____|
# | |  | | |__| | |     
# | |  | |  __  | |     
# | |__| | |  | | |____ 
#  \____/|_|  |_|\_____|
# The most customisable UHC plugin for Minecraft PE !
namespace Ad5001\UHC ; 
use pocketmine\command\CommandSender;
use pocketmine\command\Command;
use pocketmine\event\Listener;
use pocketmine\Server;
use pocketmine\level\Level;
use pocketmine\plugin\Plugin;
use pocketmine\level\particle\FloatingTextParticle;
use pocketmine\Player;
use pocketmine\utils\TextFormat as C;

use Ad5001\UHC\Main;
use Ad5001\UHC\Utils\TextParticle;
class UHCWorld {
    public function __construct(Plugin $main, Level $level, string $name, int $maxplayers, int $radius) {
        $this->p = $main;
        $this->lvl = $level;
        $this->name = $name;
        $this->maxplayers = $maxplayers;
        $this->players = [];
        $this->cfg = $main->getConfig();
        $this->radius = $radius;
    }
    public function getLevel() {
        return $this->lvl;
    }
    public function getName() {
        return $this->name;
    }
    public function getPlayers() {
        return $this->players;
    }
    public function getMaxPlayers() {
        return $this->maxplayers;
    }
    public function setPlayers(array $players) {
        foreach($players as $player) {
            if(!in_array($player, $this->players)){
                foreach($this->players as $pl) {
                    $pl->sendMessage(Main::PREFIX . C::YELLOW . "{$player->getName()} leaved the game.");
                }
            }
        }
        foreach($this->players as $player) {
            if(!in_array($player, $players)){
                foreach($this->players as $pl) {
                    $pl->sendMessage(Main::PREFIX . C::YELLOW . "{$player->getName()} joined the game.");
                    $part = new TextParticle(new FloatingTextParticle(new Vector3($this->x, $this->y, $this->z), C::GREEN . "Welcome to the UHC {$player->getName()} !\n" . C::GREEN . "To get help about the plugin , please type command /uhc howtoplay .", C::YELLOW . "-=<UHC>=-"), $this->level, $player);
                }
            }
        }
        $this->players = $players;
        return true;
    }
}