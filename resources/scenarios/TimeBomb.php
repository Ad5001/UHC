<?php

use Ad5001\UHC\scenario\Scenario;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\block\Chest;
use pocketmine\block\Air;
use pocketmine\math\Vector3;
use pocketmine\utils\TextFormats as C;

class TimeBomb extends Scenario {

    public function onDeath(PlayerDeathEvent $event) {
        $v3 = new Vector3($event->getPlayer()->x $event->getPlayer()->y, $event->getPlayer()->z);
        $v3n = new Vector3($event->getPlayer()->x $event->getPlayer()->y, $event->getPlayer()->z + 1);
        $lvl = $event->getPlayer()->getLevel();
        $lvl->setBlock($v3, new Chest());
        $lvl->setBlock($v3n, new Chest());
        $t = $level->getTile($v3);
        $t->getInventory()->setContents($event->getPlayer()->getInventory()->getContents());
        $event->getPlayer()->getInevntory()->clearAll();
        $h = $this->getServer()->getScheduler()->scheduleRepeatingTask($t = new TimeBombExplodeChestTask($this, $v3, $lvl), 20);
        $t->setHandler($h);
    }
}


class TimeBombExplodeChestTask extends \pocketmine\scheduler\PluginTask {

    public function __construct(TimeBomb $main, Vector3 $v3, \pocketmine\level\Level $lvl) {
        parent::__construct($main->getMain());
        $this->main = $main;
        $this->v3 = $v3;
        $this->seconds = 20;
        $this->lvl = $lvl;
    }


    public function onRun($tick) {
        switch($this->seconds) {
            case 20:
            $this->lvl->addParticle($this->part = new \pocketmine\level\particle\FloatingTextParticle($this->v3, C::GREEN . "before the explosion !", "20 seconds"));
            break;
            default:
            $this->part->setTitle(strval($this->seconds) . " seconds");
            break;
            case 0:
            $ex = new Explosion(\pocketmine\level\Position::fromObject($this->v3, $this->lvl), 7);
            $ex->explodeA();
            $ex->explodeB();
            $this->lvl->getTile($this->v3)->getInventory()->setContents([]);
            $this->lvl->setBlock($this->v3, new Air());
            $this->lvl->setBlock(new Vector3($this->v3->x, $this->v3->y, $this->v3->z + 1), new Air());
            $this->part->setInvisible(true);
            $this->main->getScheduler()->cancelTask($this->getTaskId());
            break;
        }
    }
}