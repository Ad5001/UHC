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

interface ScenarioInt {
    /* When the scenario is activating */
    public function onStart();
    
    
     /* When the scenario is stoping (end of UHC) */
    public function onStop(\pocketmine\Player $player);
    
    
     /* Getting the main methods */
    public function getMain();
    
    
     /* Get the config (which is a part of the config of the plugin) */
    public function getConfig();
    
    
    /* Save the config */
    public function saveConfig($cfg);
    
    
    /* Get the scenario folder */
    public function getScenariosFolder();
    
    
    /* Reload the config */
    public function reloadConfig();


}