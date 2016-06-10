<?php
#  _    _ _    _  _____ 
# | |  | | |  | |/ ____|
# | |  | | |__| | |     
# | |  | |  __  | |     
# | |__| | |  | | |____ 
#  \____/|_|  |_|\_____|
# The most customisable UHC plugin for Minecraft PE !
namespace Ad5001\UHC\event;
use pocketmine\event\Cancellable;
use Ad5001\UHC\event\UHCEvent;
use Ad5001\UHC\UHCGame;
use Ad5001\UHC\UHCWorld;


class GameStartEvent extends UHCEvent implements Cancellable {
    protected $game;
    protected $world;
    protected $players;
    static $handlerList = null;
    public function __construct($game, $world, $players) {
        $this->game = $game;
        $this->world = $world;
        $this->players = $players;
    }
    public function getWorld() {
        return $this->world;
    }
    public function getLevel() {
        return $this->world;
    }
    public function getPlayers() {
        return $this->players;
    }
}