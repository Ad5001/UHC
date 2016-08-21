<?php

use Ad5001\UHC\scenario\Scenario;
use pocketmine\Player;

class ExampleScenario extends Scenario {

    public function onStart() {

        $this->getLogger()->info("Started !");
    }

    public function onJoin(Player $player) {
        $player->sendMessage("Welcome to this example UHC Scenario !");
    }
}