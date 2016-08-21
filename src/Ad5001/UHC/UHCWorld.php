<?php
#  _    _ _    _  _____ 
# | |  | | |  | |/ ____|
# | |  | | |__| | |     
# | |  | |  __  | |     
# | |__| | |  | | |____ 
#  \____/|_|  |_|\_____|
# The most customisable UHC plugin for Minecraft PE!
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
use pocketmine\math\Vector3;

use Ad5001\UHC\Main;
use Ad5001\UHC\scenario\ScenarioManager;


class UHCWorld implements Listener {
    public function __construct(Plugin $main, Level $level, int $maxplayers, int $radius) {
        $this->p = $main;
        $this->lvl = $level;
        $this->maxplayers = $maxplayers;
        $this->players = [];
        $this->cfg = $main->getConfig();
        $this->radius = $radius;
        $main->getServer()->getPluginManager()->registerEvents($this, $main);
        $this->scenarioManager = new ScenarioManager($this->p, $this);
    }


    public function getLevel() {
        return $this->lvl;
    }


    public function isStarted() {
        return isset($this->p->UHCManager->getStartedUHCs()[$this->lvl->getName()]);
    }


    public function onEntityDamage(\pocketmine\event\entity\EntityDamageEvent $event) {
        if(!$this->isStarted()) {
            $event->setCancelled();
        }
    }


    public function onBlockBreak(\pocketmine\event\block\BlockBreakEvent $event) {
        if(!$this->isStarted()) {
            if(!$event->getPlayer()->isCreative()) {
                $event->setCancelled();
            }
        }
    }


    public function onBlockPlace(\pocketmine\event\block\BlockPlaceEvent $event) {
        if(!$this->isStarted()) {
            if(!$event->getPlayer()->isCreative()) {
                $event->setCancelled();
            }
        }
    }
    

    public function getPlayers() {
        return $this->players;
    }


    public function getName() {
        return $this->lvl->getName();
    }


    public function getMaxPlayers() {
        return $this->maxplayers;
    }

    
    public function setPlayers(array $players) {
        foreach($this->players as $key => $player) {
            if(!in_array($player, $this->players)){
                $player->sendMessage(Main::PREFIX . C::YELLOW . "You joined the game.");
                foreach($this->players as $pl) {
                    $pl->sendMessage(Main::PREFIX . C::YELLOW . "{$player->getName()} joined the game.");
                    $this->getLevel()->addParticle($part = new FloatingTextParticle(new Vector3($this->getLevel()->getSafeSpawn()->x, $this->getLevel()->getSafeSpawn()->y, $this->getLevel()->getSafeSpawn()->z), C::GREEN . "Welcome to the UHC game, {$player->getName()}!\n" . C::GREEN . "Need help? Use /uhc howtoplay.", C::YELLOW . "-=<UHC>=-"), [$player]);
                }

            }
        }
        foreach($players as $player) {
            if(!in_array($player, $players)){
                $player->sendMessage(Main::PREFIX . C::YELLOW . "You left the game.");
                foreach($this->players as $pl) {
                    $pl->sendMessage(Main::PREFIX . C::YELLOW . "{$player->getName()} left the game.");
                }
            }
        }
        $this->players = $players;
        return true;
    }
}
