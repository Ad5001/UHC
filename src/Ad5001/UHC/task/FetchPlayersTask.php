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

class FetchPlayersTask extends PluginTask {
    public function __construct(Plugin $plugin, array $worlds) {
        parent::__construct($plugin);
        $this->m = $plugin;
        $this->worlds = $worlds;
    }
    public function onRun($tick) {
        foreach($this->worlds as $world) {
            $wpls = [];
            foreach($this->m->getServer()->getOnlinePlayers() as $pl){
                if($world->getName() === $pl->getLevel()->getName() and ($pl->isSurvival())) {
                    array_push($wpls, $pl);
                }
            }
            $world->setPlayers($wpls);
            if(count($wpls) > $world->getMaxPlayers()*0.75 and count($wpls) < $world->getMaxPlayers()) {
                $this->m->UHCManager->startGame($world);
            }
        }
    }
}