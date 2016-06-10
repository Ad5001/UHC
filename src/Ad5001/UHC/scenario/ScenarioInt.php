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

interface ScenarioInt extends CommandExecutor {
    /* When the scenario is activating */
    public function onEnable();
    
    
     /* When the scenario is stoping (end of UHC) */
    public function onStop();
    
    
     /* Getting the server methods */
    public function getServer();
    
    
     /* Get the config (which is a part of the config of the plugin) */
    public function getConfig();
    
    
    /* Save the config */
    public function saveConfig();
    
    
    /* Get the scenario folder */
    public function getScenarioFolder();
    
    
    /* Reload the config */
    public function reloadConfig();
}