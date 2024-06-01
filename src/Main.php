<?php

declare(strict_types=1);

namespace banira4649\BetterKillJudgement;

use banira4649\BetterKillJudgement\event\EventListener;
use pocketmine\player\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\Server;

class Main extends PluginBase{

    /**
     * @var string[] $damagerList
     */
    private array $damagerList = [];

    public function onEnable(): void{
        $this->getServer()->getPluginManager()->registerEvents(new EventListener($this), $this);
    }

    public function getDamager(Player $player): ?Player{
        return isset($this->damagerList[$player->getName()]) ? Server::getInstance()->getPlayerExact($this->damagerList[$player->getName()]) : null;
    }

    public function resetDamager(Player $player): void{
        unset($this->damagerList[$player->getName()]);
    }

    public function setDamager(Player $player, Player $damager): void{
        $this->damagerList[$player->getName()] = $damager->getName();
    }
}