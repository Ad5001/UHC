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
use pocketmine\event\level\LevelLoadEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\event\player\PlayerRespawnEvent;
use pocketmine\event\entity\EntityRegainHealthEvent;
use pocketmine\item\Item;
use pocketmine\block\Block;
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
    const PREFIX = C::GOLD . "[" . C::DARK_RED . "UHC" . C::GOLD . "] ". C::RESET . C::WHITE;
    
    
    
    public function onLevelChange(EntityLevelChangeEvent $event) {
        foreach($this->UHCManager->getLevels() as $world) {
            if($event->getTarget()->getName() == $world->getName() and !isset($this->UHCManager->getStartedUHCs()[$world->getName()])) {
                if(count($world->getLevel()->getPlayers()) > $world->maxplayers) {
                    $event->setCancelled();
                }
            } elseif($event->getTarget()->getName() == $world->getName() and isset($this->UHCManager->getStartedUHCs()[$world->getName()]) and !isset($this->quit[$event->getEntity()->getName()])) {
                $event->getEntity()->setGamemode(3);
            } elseif($event->getTarget()->getName() == $world->getName() and isset($this->UHCManager->getStartedUHCs()[$world->getName()]) and isset($this->quit[$event->getEntity()->getName()])) {
                $this->quit = explode("/", $this->quit[$event->getEntity()->getName()]);
                if($this->quit[3] === $world->getName()) {
                    $event->getEntity()->teleport(new \pocketmine\level\Location($this->quit[0], $this->quit[1], $this->quit[2], $event->getTarget()));
                    foreach($world->getLevel()->getPlayers() as $player) {
                        $player->sendMessage(self::PREFIX . C::GREEN . "{$event->getPlayer()->getName()} back to game !");
                    }
                    $event->setCancelled();
                } else {
                    $event->getEntity()->setGamemode(3);
                }
            }
        }
    }
    
    
    public function onEnable(){
        $this->saveDefaultConfig();
        @mkdir($this->getDataFolder() . "scenarios");
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
        $this->getServer()->getPluginManager()->registerEvent("Ad5001\\UHC\\event\\GameStartEvent", $this, \pocketmine\event\EventPriority::NORMAL, new \pocketmine\plugin\MethodEventExecutor("onGameStart"), $this, true);
        $this->getServer()->getPluginManager()->registerEvent("Ad5001\\UHC\\event\\GameStopEvent", $this, \pocketmine\event\EventPriority::NORMAL, new \pocketmine\plugin\MethodEventExecutor("onGameStop"), $this, false);
        $this->UHCManager = new UHCManager($this);
        $this->quit = [];
    }



    public function onLevelLoad(\pocketmine\event\level\LevelLoadEvent $event) {
        if(isset($this->getConfig()->get("worlds")[$event->getLevel()->getName()])) {
            $this->UHCManager->registerLevel($event->getLevel());
        }
    }
    
    
    public function getClasses(string $file) {
        $tokens = token_get_all($file);
        $class_token = false;
        foreach ($tokens as $token) {
            if (is_array($token)) {
                if ($token[0] == T_CLASS) {
                    $class_token = true;
                } else if ($class_token && $token[0] == T_STRING) {
                    return $token[1];
                }
            }
        }
    }


 public function onCommand(CommandSender $sender, Command $cmd, $label, array $args){
  switch($cmd->getName()){
    case "uhc":
    if(isset($args[0]) and $sender instanceof Player) {
        switch($args[0]) {
            case "start":
            if(isset($this->UHCManager->getLevels()[$sender->getLevel()->getName()]) and !isset($this->UHCManager->getStartedUHCs()[$sender->getLevel()->getName()])) {
                $this->getLogger()->debug("Starting game {$sender->getLevel()->getName()}");
                foreach($sender->getLevel()->getPlayers() as $player) {
                    $player->sendMessage(self::PREFIX . "Starting game...");
                }
                $this->UHCManager->startUHC($sender->getLevel());
            } else {
                $sender->sendMessage("You are not in a UHC world or UHC is already started");
            }
            return true;
            break;
            case "stop":
            if(isset($this->UHCManager->getStartedUHCs()[$sender->getLevel()->getName()])) {
                $this->getLogger()->debug("Starting game {$sender->getLevel()->getName()}");
                foreach($sender->getLevel()->getPlayers() as $player) {
                    $player->sendMessage(self::PREFIX . "Starting game...");
                }
                $this->UHCManager->stopUHC($sender->getLevel());
            } else {
                $sender->sendMessage("You are not in a UHC world or UHC is already started");
            }
            return true;
            break;
            case "howtoplay":
            case "?":
            case "help":
            if(!isset($args[1])) {
                $sender->sendMessage(self::PREFIX . "Welcome to UHC's help ! WHat do you want to know about UHC?\n" . self::PREFIX . "- The game (/uhc howtoplay game)\n" . self::PREFIX . "- The commands (/uhc howtoplay commands)\n" . self::PREFIX . "- The scenarios (/uhc howtoplay scenarios)\n");
            } else {
                switch(strtolower($args[1])) {
                    case "game":
                    $sender->sendMessage(self::PREFIX . "UHC (aka Ultra HardCord) is a new difficulty where it's hardcord but with no health regen exept for golden apples and potions and only 1 life. The most popular 'UHC' Game is a PvP (Player versus Player) game where you're in this mode and you need to kill other players.\n" . self::PREFIX . "What do you want to know next?\n" . self::PREFIX . "- The commands (/uhc howtoplay commands)\n" . self::PREFIX . "- The scenarios (/uhc howtoplay scenarios)\n");
                    break;
                    case "commands":
                    $sender->sendMessage(self::PREFIX . "This UHC Plugin have special commands. For players: /scenarios will give you the list of the current enabled scenarios. " . ($sender->isOp() ? "For OP: '/uhc start' start the uhc. '/uhc stop' finish it. '/scenarios add <scenario>' adds a scenario. '/scenario rm <scenario>' remove a scenario. '/scenarios list' gives you the list of installed scenarios.\n" : "\n") . self::PREFIX . "What do you want to know next?\n" . self::PREFIX . "- The game (/uhc howtoplay game)\n" . self::PREFIX . "- The scenarios (/uhc howtoplay scenarios)");
                    break;
                    case "scenarios":
                    $sender->sendMessage(self::PREFIX . "Scenarios are 'addons' to the UHC that modify some mechanics or some parts of the game. You can download them or create them yourself. Look at ccommands if you want to know how to add a scenario to a game and look at https://github.com/Ad5001/UHC/wiki/What-are-Scenarios%3F. If you want to know more about a scenario, use /uhc howtoplay <scenario name>.\n" . self::PREFIX . "What do you want to know next?\n"  . self::PREFIX . "- The game (/uhc howtoplay game)\n" . self::PREFIX . "- The commands (/uhc howtoplay commands)\n");
                    break;
                    default:
                    if(in_array($args[1], $this->UHCManager->getLevels()[$sender->getLevel()->getName()]->scenarioManager->getScenarios())) {
                        $sender->sendMessage(self::PREFIX . "Help for $args[1]. " . $args[1]::help());
                    } else {
                        $sender->sendMessage(self::PREFIX . "No help for $args[1]");
                    }
                    break;
                    
                }
            }
            return true;
            break;
        }
    }
    break;
    case "scenarios":
        if(isset($args[0]) and $sender instanceof Player and $sender->hasPermission("uhc.scenarios.modify")) {
             if(isset($this->UHCManager->getLevels()[$sender->getLevel()->getName()]) and !isset($this->UHCManager->getStartedUHCs()[$sender->getLevel()->getName()])) {
                     switch($args[0]) {
                         case "add":
                         if(isset($args[1])) {
                         if(isset($this->UHCManager->getLevels()[$sender->getLevel()->getName()]->scenarioManager->getScenarios()[$args[1]])) {
                             if(!isset($this->UHCManager->getLevels()[$sender->getLevel()->getName()]->scenarioManager->getUsedScenarios()[$args[1]])) {
                                 $this->UHCManager->getLevels()[$sender->getLevel()->getName()]->scenarioManager->addScenario($args[1]);
                                 foreach($sender->getLevel()->getPlayers() as $p) {
                                     $p->sendTip(C::GOLD . C::BOLD . "Scenario added !" . PHP_EOL . C::RESET . C::GREEN . C::ITALIC . "+ " . $args[1]);
                                 }
                                 $sender->sendMessage(self::PREFIX . C::GREEN . " Succefully added scenario $args[1] !");
                             } else {
                                 $sender->sendMessage(self::PREFIX . C::DARK_RED . "Scenario $args[1] has already been added !");
                             }
                         } else {
                             $sender->sendMessage(self::PREFIX . C::DARK_RED . "Scenario $args[1] does not exists !");
                         }
                         } else {
                             $sender->sendMessage(self::PREFIX . C::DARK_RED . "Usage: /scenarios add <scenario>");
                         }
                         break;
                         case "remove":
                         case "rm":
                         case "delete":
                         case "del":
                         if(isset($args[1])) {
                         if(isset($this->UHCManager->getLevels()[$sender->getLevel()->getName()]->scenarioManager->getUsedScenarios()[$args[1]])) {
                             if(isset($this->UHCManager->getLevels()[$sender->getLevel()->getName()]->scenarioManager->getUsedScenarios()[$args[1]])) {
                                 $this->UHCManager->getLevels()[$sender->getLevel()->getName()]->scenarioManager->rmScenario($args[1]);
                                 foreach($sender->getLevel()->getPlayers() as $p) {
                                     $p->sendTip(C::GOLD . C::BOLD . "Scenario added !" . PHP_EOL . C::RESET . C::GREEN . C::ITALIC . "+ " . $args[1]);
                                 }
                                 $sender->sendMessage(self::PREFIX . C::GREEN . " Succefully removed scenario $args[1] !");
                             } else {
                                 $sender->sendMessage(slef::PREFIX . C::DARK_RED . "Scenario $args[1] hasn't been added yet !");
                             }
                         } else {
                             $sender->sendMessage(self::PREFIX . C::DARK_RED . "Scenario $args[1] does not exists !");
                         }
                         } else {
                             $sender->sendMessage(self::PREFIX . C::DARK_RED . "Usage: /scenarios rm <scenario>");
                         }
                         break;
                         case "list":
                         $sender->sendMessage(self::PREFIX . "Current server's scenarios: " . implode(", ", $this->UHCManager->getLevels()[$sender->getLevel()->getName()]->scenarioManager->getScenarios()));
                         break;
                     }
             } else {
                 $sender->sendMessage(self::PREFIX . "You're not in a UHC world !");
             }
        } else {
            if(isset($this->UHCManager->getLevels()[$sender->getLevel()->getName()]) and !isset($this->UHCManager->getStartedUHCs()[$sender->getLevel()->getName()])) {
                $scs = $this->UHCManager->getLevels()[$sender->getLevel()->getName()]->scenarioManager->getUsedScenarios();
                $sender->sendMessage(self::PREFIX . "Current enabled scenarios : " . implode(", ", $scs));
            }
        }
        return true;
    break;
}
return false;
 }






 # Event Listener !

 public function onInteract(\pocketmine\event\player\PlayerInteractEvent $event) {
       if(isset($this->UHCManager->getLevels()[$event->getPlayer()->getLevel()->getName()])) {
           foreach($this->UHCManager->getLevels()[$event->getPlayer()->getLevel()->getName()]->scenarioManager->getUsedScenarios() as $sc) {
               $sc->onInteract($event);
           }
       }
   }


   public function onEntityLevelChange(EntityLevelChangeEvent $event) {
       if(isset($this->UHCManager->getLevels()[$event->getOrigin()->getName()]) and $event->getEntity() instanceof Player) {
           $this->quit[$event->getEntity()->getName()] = $event->getEntity()->x . "/" . $event->getEntity()->y . "/" . $event->getEntity()->z. "/" . $event->getOrigin()->getName();
           foreach($this->UHCManager->getLevels()[$event->getOrigin()->getName()]->scenarioManager->getUsedScenarios() as $sc) {
               $sc->onQuit($event->getEntity());
           }
       }
       if(isset($this->UHCManager->getLevels()[$event->getTarget()->getName()]) and $event->getEntity() instanceof Player) {
           foreach($this->UHCManager->getLevels()[$event->getTarget()->getName()]->scenarioManager->getUsedScenarios() as $sc) {
               $sc->onJoin($event->getEntity());
           }
       }
   }


   public function onPlayerChat(\pocketmine\event\player\PlayerChatEvent $event) {
       if(isset($this->UHCManager->getLevels()[$event->getPlayer()->getLevel()->getName()])) {
           foreach($this->UHCManager->getLevels()[$event->getPlayer()->getLevel()->getName()]->scenarioManager->getUsedScenarios() as $sc) {
               $sc->onPlayerChat($event);
               $sc->onChat($event);
           }
       }
   }


   public function onPlayerCommandPreprocess(\pocketmine\event\player\PlayerCommandPreprocessEvent $event) {
       if(isset($this->UHCManager->getLevels()[$event->getPlayer()->getLevel()->getName()])) {
           foreach($this->UHCManager->getLevels()[$event->getPlayer()->getLevel()->getName()]->scenarioManager->getUsedScenarios() as $sc) {
               $sc->onPlayerCommand($event);
           }
       }
   }


   public function onPlayerDeath(\pocketmine\event\player\PlayerDeathEvent $event) {
       if(isset($this->UHCManager->getLevels()[$event->getPlayer()->getLevel()->getName()])) {
           foreach($this->UHCManager->getLevels()[$event->getPlayer()->getLevel()->getName()]->scenarioManager->getUsedScenarios() as $sc) {
               $sc->onPlayerDeath($event);
               $sc->onDeath($event);
           }
       }
   }


   public function onPlayerDropItem(\pocketmine\event\player\PlayerDropItemEvent $event) {
       if(isset($this->UHCManager->getLevels()[$event->getPlayer()->getLevel()->getName()])) {
       foreach($this->UHCManager->getLevels()[$event->getPlayer()->getLevel()->getName()]->scenarioManager->getUsedScenarios() as $sc) {
               $sc->onPlayerDropItem($event);
               $sc->onPlayerDropItem($event);
           }
       }
   }


   public function onPlayerMove(\pocketmine\event\player\PlayerMoveEvent $event) {
       if(isset($this->UHCManager->getLevels()[$event->getPlayer()->getLevel()->getName()])) {
           foreach($this->UHCManager->getLevels()[$event->getPlayer()->getLevel()->getName()]->scenarioManager->getUsedScenarios() as $sc) {
               $sc->onPlayerMove($event);
               $sc->onMove($event);
           }
       }
   }


   public function onPlayerItemConsume(\pocketmine\event\player\PlayerItemConsumeEvent $event) {
       if(isset($this->UHCManager->getLevels()[$event->getPlayer()->getLevel()->getName()])) {
           foreach($this->UHCManager->getLevels()[$event->getPlayer()->getLevel()->getName()]->scenarioManager->getUsedScenarios() as $sc) {
               $sc->onPlayerItemConsume($event);
               $sc->onItemConsume($event);
           }
       }
   }


   public function onPlayerItemHeld(\pocketmine\event\player\PlayerItemHeldEvent $event) {
       if(isset($this->UHCManager->getLevels()[$event->getPlayer()->getLevel()->getName()])) {
           foreach($this->UHCManager->getLevels()[$event->getPlayer()->getLevel()->getName()]->scenarioManager->getUsedScenarios() as $sc) {
               $sc->onPlayerItemHeld($event);
               $sc->onItemHeld($event);
           }
       }
   }


   public function onBlockBreak(\pocketmine\event\block\BlockBreakEvent $event) {
       if(isset($this->UHCManager->getLevels()[$event->getPlayer()->getLevel()->getName()])) {
           foreach($this->UHCManager->getLevels()[$event->getPlayer()->getLevel()->getName()]->scenarioManager->getUsedScenarios() as $sc) {
               $sc->onBlockBreak($event);
               $sc->onBreak($event);
           }
       }
   }


   public function onBlockPlace(\pocketmine\event\block\BlockPlaceEvent $event) {
       if(isset($this->UHCManager->getLevels()[$event->getPlayer()->getLevel()->getName()])) {
           foreach($this->UHCManager->getLevels()[$event->getPlayer()->getLevel()->getName()]->scenarioManager->getUsedScenarios() as $sc) {
               $sc->onBlockPlace($event);
               $sc->onPlace($event);
           }
       }
   }


   public function onEntityDamage(\pocketmine\event\entity\EntityDamageEvent $event) {
       if(isset($this->UHCManager->getLevels()[$event->getEntity()->getLevel()->getName()])) {
           foreach($this->UHCManager->getLevels()[$event->getEntity()->getLevel()->getName()]->scenarioManager->getUsedScenarios() as $sc) {
               $sc->onEntityDamage($event);
           }
       }
   }


   public function onProjectileLaunch(\pocketmine\event\entity\ProjectileLaunchEvent $event) {
       if(isset($this->UHCManager->getLevels()[$event->getEntity()->getLevel()->getName()])) {
           foreach($this->UHCManager->getLevels()[$event->getEntity()->getLevel()->getName()]->scenarioManager->getUsedScenarios() as $sc) {
               $sc->onProjectileLaunch($event);
           }
       }
   }


   public function onProjectileHit(\pocketmine\event\entity\ProjectileHitEvent $event) {
       if(isset($this->UHCManager->getLevels()[$event->getEntity()->getLevel()->getName()])) {
           foreach($this->UHCManager->getLevels()[$event->getEntity()->getLevel()->getName()]->scenarioManager->getUsedScenarios() as $sc) {
               $sc->onProjectileHit($event);
           }
       }
   }


   public function onDataPacketReceive(\pocketmine\event\server\DataPacketReceiveEvent $event) {
       if(isset($this->UHCManager->getLevels()[$event->getPlayer()->getLevel()->getName()])) {
           foreach($this->UHCManager->getLevels()[$event->getPlayer()->getLevel()->getName()]->scenarioManager->getUsedScenarios() as $sc) {
               $sc->onDataPacketReceive($event);
           }
       }
   }


   public function onDataPacketSend(\pocketmine\event\server\DataPacketSendEvent $event) {
       if(isset($this->UHCManager->getLevels()[$event->getPlayer()->getLevel()->getName()])) {
           foreach($this->UHCManager->getLevels()[$event->getPlayer()->getLevel()->getName()]->scenarioManager->getUsedScenarios() as $sc) {
               $sc->onDataPacketSend($event);
           }
       }
   }


   public function onServerCommand(\pocketmine\event\server\ServerCommandEvent $event) {
       foreach($this->UHCManager->getLevels() as $lvl => $world) {
           foreach($world->scenarioManager->getUsedScenarios() as $sc) {
               $sc->onServerCommand($event);
           }
       }
   }

   public function onPlayerJoin(\pocketmine\event\player\PlayerJoinEvent $event) {
        if(!isset($this->ft)) {
            $this->ft = $this->getServer()->getScheduler()->scheduleRepeatingTask(new FetchPlayersTask($this, $this->UHCManager->getStartedUHCs()), 10);
        }
        if(isset($this->quit[$event->getPlayer()->getName()]) and $event->getPlayer()->getLevel()->getName() == $this->quit[3]) {
                $this->quit = explode("/", $this->quit[$event->getPlayer()->getName()]);
                $event->getPlayer()->teleport($this->getServer()->getLevelByName($this->quit[3]));
                $event->getPlayer()->teleport(new Vector3($this->quit[0], $this->quit[1], $this->quit[2]));
                foreach($this->getServer()->getLevelByName($this->quit[3])->getLevel()->getPlayers() as $player) {
                    $player->sendMessage(self::PREFIX . C::GREEN . "{$event->getPlayer()->getName()} back to game !");
                }
        }
       if(isset($this->UHCManager->getLevels()[$event->getPlayer()->getLevel()->getName()])) {
           foreach($this->UHCManager->getLevels()[$event->getPlayer()->getLevel()->getName()]->scenarioManager->getUsedScenarios() as $sc) {
               $sc->onJoin($event->getPlayer());
           }
       }

   }

   public function onPlayerQuit(\pocketmine\event\player\PlayerQuitEvent $event) {
       if(isset($this->UHCManager->getLevels()[$event->getPlayer()->getLevel()->getName()])) {
           $this->quit[$event->getPlayer()->getName()] = $event->getPlayer()->x . "/" . $event->getPlayer()->y . "/" . $event->getPlayer()->z. "/" . $event->getPlayer()->getLevel()->getName();
           foreach($this->UHCManager->getLevels()[$event->getPlayer()->getLevel()->getName()]->scenarioManager->getUsedScenarios() as $sc) {
               $sc->onQuit($event->getPlayer());
           }
       }
   }


    public function onGameStart(\Ad5001\UHC\event\GameStartEvent $event) {}


    public function onGameStop(\Ad5001\UHC\event\GameStopEvent $event) {}


}
