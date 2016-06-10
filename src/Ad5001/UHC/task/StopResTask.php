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
use Ad5001\UHC\Main;

class StopResTask extends PluginTask {
    public function __construct(Plugin $plugin, array $players) {
        parent::__construct($this);
        $this->m = $plugin;
        $this->players = $players;
    }
    public function onRun($tick) {
        foreach($this->players as $player) {
            $player->sendMessage(Main::PREFIX . C::RED . "You can now take damage.");
        }
    }
}