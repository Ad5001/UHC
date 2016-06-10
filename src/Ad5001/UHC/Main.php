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
use pocketmine\event\level\LevelLoadEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\event\player\PlayerRespawnEvent;
use pocketmine\event\entity\EntityRegainHealthEvent;
use pocketmine\plugin\PluginBase;
use pocketmine\Server;
use pocketmine\Player;
use pocketmine\event\entity\EntityLevelChangeEvent;
use pocketmine\utils\TextFormat as C;
 
use Ad5001\UHC\UHCWorld;
use Ad5001\UHC\UHCGame;
use Ad5001\UHC\task\FetchPlayersTask;
use Ad5001\UHC\task\StartGameTask;
use Ad5001\UHC\event\GameStartEvent;
use Ad5001\UHC\event\GameFinishEvent;
class Main extends PluginBase implements Listener{
    const PREFIX = C::GOLD . "[" . C::DARK_RED . "UHC" . C::GOLD . "] ". C::RESET;
    
    
    
    public function startGame(UHCWorld $world) {
        $ft = $this->getServer()->getScheduler()->scheduleRepeatingTask(new StartGameTask($this, $world), 20);
        // $this->games[$world->getName()] = new UHCGame($this, $world);
    } 
    
    
    
    public function onLevelChange(EntityLevelChangeEvent $event) {
        foreach($this->worlds as $world) {
            if($event->getLevel()->getName() === $world->getName() and !isset($this->games[$world->getName()])) {
                if(count($world->getLevel()->getPlayers) > $world->maxplayers) {
                    $event->setCancelled();
                }
            } elseif($event->getLevel()->getName() === $world->getName() and isset($this->games[$world->getName()]) and !isset($this->quit[$event->getPlayer()])) {
                $event->getPlayer()->setGamemode(3);
            } elseif($event->getLevel()->getName() === $world->getName() and isset($this->games[$world->getName()]) and isset($this->quit[$event->getPlayer()])) {
                $quit = explode("/", $this->quit[$event->getPlayer()]);
                if($quit[3] === $world->getName()) {
                    $event->getPlayer()->teleport(new Vector3($quit[0], $quit[1], $quit[2]));
                    foreach($world->getLevel()->getPlayers() as $player) {
                        $player->sendMessage(self::PREFIX . C::GREEN . "{$event->getPlayer()->getName()} back to game !");
                    }
                }
            }
        }
    }
    
    
    
    
    public function onPlayerJoin(PlayerJoinEvent $event) {
        if(!isset($this->ft)) {
            $this->ft = $this->getServer()->getScheduler()->scheduleRepeatingTask(new FetchPlayersTask($this, $this->worlds), 10);
            foreach($this->getConfig()->get("worlds") as $lvl) {
                $this->worlds[$lvl["name"]] = new UHCWorld($this, $this->getServer()->getLevelByName($lvl["name"]), $lvl["name"], $lvl["maxplayers"], $lvl["radius"]);
                $this->getLogger()->debug("Processing {$lvl["name"]}");
            }
        }
        if(isset($this->quit[$event->getPlayer()->getName()])) {
                $quit = explode("/", $this->quit[$event->getPlayer()->getName()]);
                $event->getPlayer()->teleport($this->getServer()->getLevelByName($quit[4]));
                $event->getPlayer()->teleport(new Vector3($quit[0], $quit[1], $quit[2]));
                foreach($world->getLevel()->getPlayers() as $player) {
                    $player->sendMessage(self::PREFIX . C::GREEN . "{$event->getPlayer()->getName()} back to game !");
                }
        }
    }
    
    
    
    
    public function onEnable(){
        $this->reloadConfig();
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
        $this->worlds = [];
        $this->games = [];
        $this->quit = [];
    }
 
 
 
 
public function onLoad(){
$this->reloadConfig();
$this->saveDefaultConfig();
}

public function onRespawn(PlayerRespawnEvent $event) {
    foreach($this->games as $game) {
        $game->onRespawn($event);
    }
}

public function onPlayerQuit(PlayerQuitEvent $event) {
    foreach($this->games as $game) {
        $game->onPlayerQuit($event);
    }
}

public function onPlayerDeath(PlayerDeathEvent $event) {
    foreach($this->games as $game) {
        $game->onPlayerDeath($event);
    }
}

public function onHeal(EntityRegainHealthEvent $event) {
    foreach($this->games as $game) {
        $game->onHeal($event);
    }
}


 public function onCommand(CommandSender $sender, Command $cmd, $label, array $args){
switch($cmd->getName()){
    case "uhc":
    if(isset($args[0]) and $sender instanceof Player) {
        switch($args[0]) {
            case "start":
            if(isset($this->worlds[$sender->getLevel()->getName()]) and !isset($this->games[$sender->getLevel()->getName()])) {
                $this->getLogger()->debug("Starting game {$sender->getLevel()->getName()}");
                foreach($sender->getLevel()->getPlayers() as $player) {
                    $player->sendMessage(self::PREFIX . "Starting game...");
                }
                $this->startGame($this->worlds[$sender->getLevel()->getName()]);
            } else {
                $sender->sendMessage("You are not in a UHC world or UHC is already started");
            }
            return true;
            break;
            case "tp":
            if(isset($this->worlds[$sender->getLevel()->getName()]) and isset($this->games[$sender->getLevel()->getName()]) and $sender->getGamemode() === 3) {
                if(isset($args[1])) {
                    if($this->getServer()->getPlayer($args[1])->getName() ===! null) {
                        $player = $this->getServer()->getPlayer($args[1]);
                        $sender->teleport(new Vector3($player->x, $player->y, $player->z), $player->yaw, $player->pitch);
                    } else {
                        $sender->sendMessage(self::PREFIX . "Player {$args[1]} does NOT exists");
                    }
                }  else {
                        $sender->sendMessage(self::PREFIX . "Usage: /uhc tp <player>");
                }
            }  else {
                        $sender->sendMessage(self::PREFIX . "Either you're not in a UHC Game or in gamemode 3");
                }
            return true;
            break;
        }
    }
    break;
    case "scenarios" {
        if(isset($args[2])) {
             if(isset($this->worlds[$sender->getLevel()->getName()]) and !isset($this->games[$sender->getLevel()->getName()])) {
                 if(file_exists($this->getDataFolder() . "scenarios/" . $args[2] . ".php")) { // yes, I'm treating args[2] before args[1] but who cares x) ?
                     switch($args[1]) {
                         case "add":
                         $sl = new \pocketmine\plugin\ScriptPluginLoader($this->getServer());
                         $scenarios[$args[2]] = $sl->load(realpath($this->getDataFolder() . "scenarios/" . $args[2] . ".php"));
                         $scenarios[$args[2]]->onEnable();
                         break;
                         case "remove":
                         $scenarios[$args[2]]->onStop();
                         unset($scenarios[$args[2]]);
                         break;
                     }
                 }
             }
        }
    }
}
return false;
 }
}