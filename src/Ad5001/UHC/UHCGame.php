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
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\event\player\PlayerRespawnEvent;
use pocketmine\event\entity\EntityRegainHealthEvent;
use pocketmine\level\Level;
use pocketmine\plugin\Plugin;
use pocketmine\math\Vector3;
use pocketmine\utils\TextFormat as C;
use pocketmine\Player;



use Ad5001\UHC\UHCWorld;
use Ad5001\UHC\task\StopResTask;
use Ad5001\UHC\Main;
use Ad5001\UHC\event\GameStartEvent;




class UHCGame implements Listener{
    public function __construct(Plugin $plugin, UHCWorld $world) {
        $this->m = $plugin;
        $this->world = $world;
        $plugin->getServer()->getPluginManager()->registerEvents($this, $plugin);
        $this->players = $world->getLevel()->getPlayers();
        $event = new GameStartEvent($this, $world, $this->players);
        $this->m->getServer()->getPluginManager()->callEvent($event);
        $this->cancelled = false;
        $this->kills = [];
        if($event->isCancelled()) {
            $this->cancelled = true;
        } else {
            $radius = $world->radius;
            foreach($this->players as $player) {
                $x = rand($radius + $world->getLevel()->getSpawnLocation()->x, $world->getLevel()->getSpawnLocation()->x - $radius);
                $z = rand($radius + $world->getLevel()->getSpawnLocation()->z, $world->getLevel()->getSpawnLocation()->z - $radius);
                $pos = new Vector3($x, 128, $z);
                $player->teleport($pos);
                $effect = \pocketmine\entity\Effect::getEffect(11);
                $effect->setDuration(30*20);
                $effect->setAmplifier(99);
                $effect->setVisible(false);
                $player->addEffect($effect);
                $this->m->getServer()->getScheduler()->scheduleDelayedTask(new StopResTask($this, $this->world), 30*20);
                $player->sendMessage(Main::PREFIX . C::GREEN . "Game started ! Good luck {$player->getName()} !");
            }
        }
    }
    
    
    public function onHeal(EntityRegainHealthEvent $event) {
        if($event->getEntity() instanceof Player and $event->getRegainReason() === EntityRegainHealthEvent::CAUSE_SATURATION and $event->getEntity()->getLevel()->getName() === $world->getLevel()->getName()) { // if player is playing
            $event->setCancelled();
        }
    }
    
    
    public function onRespawn(PlayerRespawnEvent $event) {
        if(isset($this->respawn[$event->getPlayer()->getName()]) and !$this->cancelled) {
            $player->teleport($this->world->getLevel());
            $player->setGamemode(3);
            unset($this->respawn[$event->getPlayer()->getName()]);
        }
    }
    
    
    public function onPlayerQuit(PlayerQuitEvent $event) {
        if($event->getPlayer()->getLevel()->getName() === $this->world->getLevel()->getName()) {
            $this->m->quit[$event->getPlayer()->getName()] = "{$event->getPlayer()->x}/{$event->getPlayer()->y}/{$event->getPlayer()->z}/{$event->getPlayer()->getLevel()->getName()}/";
        }
    }
    
    
    public function onPlayerDeath(PlayerDeathEvent $event) {
        if($event->getPlayer()->getLevel()->getName() === $this->world->getName() and !$this->cancelled) {
            $players = $this->world->getPlayers();
            unset($players[$event->getPlayer()]);
            $this->worlds->setPlayers($players);
            $event->setDeathMessage(Main::PREFIX . C::YELLOW . $event->getDeathMessage());
            $this->respawn[$event->getPlayer()->getName()] = true;
            $pls = [];
            foreach($this->players as $pl) {
                array_push($pls, $pl);
            }
            $cause = $event->getEntity()->getLastDamageCause();
            if($cause instanceof \pocketmine\event\entity\EntityDamageByEntityEvent){
                $killer = $cause->getDamager();
                if($killer instanceof Player){
                    if(isset($this->kills[$killer->getName()])) {
                        $this->kills[$killer->getName()]++;
                    } else {
                        $this->kills[$killer->getName()] = 1;
                    }
                } else {
                    if(isset($this->kills[C::GREEN . "P" . C::BLUE . "v" . C::RED . "E"])) {
                        $this->kills[C::GREEN . "P" . C::BLUE . "v" . C::RED . "E"]++;
                    } else {
                        $this->kills[C::GREEN . "P" . C::BLUE . "v" . C::RED . "E"] = 1;
                    }
                }
            } else {
                if(isset($this->kills[C::GREEN . "P" . C::BLUE . "v" . C::RED . "E"])) {
                    $this->kills[C::GREEN . "P" . C::BLUE . "v" . C::RED . "E"]++;
                } else {
                    $this->kills[C::GREEN . "P" . C::BLUE . "v" . C::RED . "E"] = 1;
                }
            }
            if(count($pls === 1)) {
                $this->stop($pls[0]);
            }
        }
    }
    
    
    public function stop(Player $winner) {
        if(!$this->cancelled) {
            $event = $this->getServer()->getPluginManager()->callEvent(new GameFinishEvent($this, $this->world, $winner));
            if(!$event->isCancelled()) {
                foreach($this->players as $player) {
                    $player->sendMessage(Main::PREFIX . C::YELLOW . $winner->getName());
                    $player->teleport($this->m->getServer()->getLevelByName($this->m->getConfig()->get("LobbyWorld")));
                }
            }
        }
    }
    
    
    public function getPlayers() {
        return $this->world->getPlayers();
    }
    
    
    public function onPlayerChat(PlayerChatEvent $event) {
        if($event->getPlayer()->getLevel()->getName() === $this->world->getLevel()->getName() and $event->getPlayer()->getGamemode() === 3) {
            if($event->getPlayer()->isSpectator()) {
                foreach($this->world->getLevel()->getPlayer() as $player) {
                    $player->sendMessage(C::GRAY . "[SPECTATOR] {$event->getPlayer()->getName()} > " . $event->getMessage());
                    
                }
                $event->setCancelled(true);
            }
        }
    }
    
    /*
    Will be useful for scenarios:
    @param player
    */
    public function getKills(Player $player) {
        if(isset($this->kills[$player->getName()])) {
            return $this->kills[$player->getName()];
        } else {
            return null;
        }
    }
    /*
    Will be useful for scenarios too:
    @param player
    */
    public function addKills(Player $player, int $count) {
        if(isset($this->kills[$player->getName()])) {
            $this->kills[$player->getName()] += $count;
        } else {
            $this->kills[$player->getName()] = $count;
        }
        return true;
    }
}