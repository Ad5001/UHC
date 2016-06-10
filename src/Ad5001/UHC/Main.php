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
use pocketmine\plugin\PluginBase;
use pocketmine\Server;
use pocketmine\Player;
use pocketmine\utils\TextFormat as C;
 
use Ad5001\UHC\UHCWorld;
use Ad5001\UHC\UHCGame;
use Ad5001\UHC\task\FetchPlayersTask;
use Ad5001\UHC\event\GameStartEvent;
use Ad5001\UHC\event\GameFinishEvent;
class Main extends PluginBase implements Listener{
    const PREFIX = C::GOLD . "[" . C::DARK_RED . "UHC" . C::GOLD . "] ". C::RESET;
    
    
    
    public function startGame(UHCWorld $world) {
        $this->games[$world->getName()] = new UHCGame($this, $world);
    } 
    
    
    
    public function onLevelChange(\pocketmine\event\entity\EntityLevelChangeEvent $event) {
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
        }
        } elseif(isset($this->quit[$event->getPlayer()])) {
                $quit = explode("/", $this->quit[$event->getPlayer()]);
                $event->getPlayer()->teleport($this->getServer()->getLevelByName($quit[4]));
                $event->getPlayer()->teleport(new Vector3($quit[0], $quit[1], $quit[2]));
                foreach($world->getLevel()->getPlayers() as $player) {
                    $player->sendMessage(self::PREFIX . C::GREEN . "{$event->getPlayer()->getName()} back to game !");
                }
        }
    }
    
    
    
    
    public function onLevelLoad(LevelLoadEvent $event) {
        foreach($this->getConfig()->get("worlds") as $lvl) {
            $this->getLogger()->debug("Processing $lvl");
            if($event->getLevel()->getName() === $lvl["name"]) {
                $this->world[$lvl["name"]] = new UHCWorld($this, $this->getServer()->getLevelByName($lvl), $lvl["name"], $lvl["maxplayers"], $lvl["radius"]);
                $this->getLogger()->debug("Processing $lvl = {$event->getLevel()->getName()}");
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




 public function onCommand(CommandSender $sender, Command $cmd, $label, array $args){
switch($cmd->getName()){
    case "uhc":
    if(isset($args[0]) and $sender instanceof Player) {
        switch($args[0]) {
            case "start":
            if(isset($this->worlds[$sender->getLevel()->getName()]) and !isset($this->games[$sender->getLevel()->getName()])) {
                $this->getLogger()->debug("Starting game {$this->worlds[$sender->getLevel()->getName()]}");
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
}
return false;
 }
}