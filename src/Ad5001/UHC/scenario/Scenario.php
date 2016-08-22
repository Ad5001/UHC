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
use pocketmine\Player;
use pocketmine\event\Listener;
use pocketmine\utils\Config;
use Ad5001\UHC\scenario\ScenarioInt;
use Ad5001\UHC\Main;
use Ad5001\UHC\UHCWorld;

abstract class Scenario implements ScenarioInt, Listener {
    
    private $server;
    
    private $game;
    
    private $level;
    
    public function __construct(Server $server, UHCWorld $level) {
        $this->server = $server;
        $this->level = $level;
    }
    
    
    public function onStart() {}
    
    
    public function onStop() {}


    public function onJoin(Player $player) {}


    public function onQuit(Player $player) {}


    public function getLevel() {
        return $this->level;
    }


    public function getGame() {
        if(isset($this->main->UHCManager->getStartedUHCs()[$this->level->getName()])) {
            return $this->main->UHCManager->getStartedUHCs()[$this->level->getName()];
        }
        return null;
    }
    

    public function onInteract(\pocketmine\event\player\PlayerInteractEvent $event) {}


   public function onChat(\pocketmine\event\player\PlayerChatEvent $event) {}


   public function onPlayerChat(\pocketmine\event\player\PlayerChatEvent $event) {}


   public function onPlayerCommand(\pocketmine\event\player\PlayerCommandPreprocessEvent $event) {}


   public function onDeath(\pocketmine\event\player\PlayerDeathEvent $event) {}


   public function onPlayerDeath(\pocketmine\event\player\PlayerDeathEvent $event) {}


   public function onPlayerDropItem(\pocketmine\event\player\PlayerDropItemEvent $event) {}


   public function onDrop(\pocketmine\event\player\PlayerDropItemEvent $event) {}


   public function onPlayerMove(\pocketmine\event\player\PlayerMoveEvent $event) {}


   public function onMove(\pocketmine\event\player\PlayerMoveEvent $event) {}


   public function onPlayerItemConsume(\pocketmine\event\player\PlayerItemConsumeEvent $event) {}


   public function onItemConsume(\pocketmine\event\player\PlayerItemConsumeEvent $event) {}


   public function onPlayerItemHeld(\pocketmine\event\player\PlayerItemHeldEvent $event) {}


   public function onItemHeld(\pocketmine\event\player\PlayerItemHeldEvent $event) {}


   public function onDataPacketReceive(\pocketmine\event\server\DataPacketReceiveEvent $event) {}


   public function onDataPacketSend(\pocketmine\event\server\DataPacketSendEvent $event) {}


   public function onServerCommand(\pocketmine\event\server\ServerCommandEvent $event) {}


   public function onBlockBreak(\pocketmine\event\block\BlockBreakEvent $event) {}


   public function onBreak(\pocketmine\event\block\BlockBreakEvent $event) {}


   public function onBlockPlace(\pocketmine\event\block\BlockPlaceEvent $event) {}


   public function onPlace(\pocketmine\event\block\BlockPlaceEvent $event) {}


   public function onEntityDamage(\pocketmine\event\entity\EntityDamageEvent $event) {}


   public function onProjectileLaunch(\pocketmine\event\entity\ProjectileLauchEvent $event) {}


   public function onProjectileHit(\pocketmine\event\entity\ProjectileHitEvent $event) {}
    
    
    public function getMain() {
        return $this->server->getPluginManager()->getPlugin("UHC");
    }


    public function getServer() {
        return $this->main->getServer();
    }


    public static function help() {
        return "This scenario does not purpose help.";
    }


    public function getLogger() {
        return $this->getMain()->getLogger();
    }
    
    
    
    public function getConfig() {
        return $this->getMain()->getConfig()->get("Scenarios")[$this->name];
    }
    
    
    
    public function reloadConfig() {
        $this->getMain()->reloadConfig();
        return $this->getMain()->getConfig()->get("Scenarios")[$this->name];
    }
    
    
    
    public function saveConfig($cfg) {
        $scenarios = $this->getMain()->getConfig()->get("Scenarios");
        $scenarios[$this->name] = $cfg;
        $this->getMain()->getConfig()->set("Scenarios", $scenarios);
        return $this->getMain()->getConfig->save();
    }
    
    
    
    public function getScenariosFolder() {
        return $this->getMain()->getDataFolder() . "scenarios/";
    }
}