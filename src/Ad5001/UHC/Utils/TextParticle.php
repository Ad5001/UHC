<?php
namespace Ad5001\UHC\Utils;

use pocketmine\level\particle\FloatingTextParticle;
use pocketmine\level\Level;
use pocketmine\Player;

class TextParticle {
    public function __construct(FloatingTextParticle $particle, Level $level, Player $player) {
        $pk = $particle->encode();

		if ($player === null) {
			if ($pk !== null) {
				if (!is_array($pk)) {
					$level->addChunkPacket($particle->x >> 4, $particle->z >> 4, $pk);
				} else {
					foreach ($pk as $e) {
						$level->addChunkPacket($particle->x >> 4, $particle->z >> 4, $e);
					}
				}
			}
		} else {
			if ($pk !== null) {
                $player->dataPacket($packet);
			}
    }
}