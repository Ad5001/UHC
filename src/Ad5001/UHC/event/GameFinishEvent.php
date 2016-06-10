<?php
#  _    _ _    _  _____ 
# | |  | | |  | |/ ____|
# | |  | | |__| | |     
# | |  | |  __  | |     
# | |__| | |  | | |____ 
#  \____/|_|  |_|\_____|
# The most customisable UHC plugin for Minecraft PE !
namespace Ad5001\UHC\event;
use pocketmine\event\Cancellable.php;
use Ad5001\UHC\event\UHCEvent;
use Ad5001\UHC\UHCGame;
use Ad5001\UHC\UHCWorld;

protected $game;
protected $world;
protected $winner;
class GameFinishEvent extends UHCEvent implements Cancellable {
    public function __construct($game, $world, $winner) {
        $this->game = $game;
        $this->world = $world;
        $this->winner = $winner;
    }
    public function getWorld() {
        return $this->world;
    }
    public function getLevel() {
        return $this->world;
    }
    public function getWinner() {
        return $this->winner;
    }
    public function setWinner(Player $winner) {
        $this->winner = $winner;
    }
}