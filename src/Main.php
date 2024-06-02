<?php

declare(strict_types=1);

namespace banira4649\BetterKillJudgement;

use banira4649\BetterKillJudgement\event\EventListener;
use pocketmine\player\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\scheduler\ClosureTask;
use pocketmine\scheduler\TaskHandler;
use pocketmine\Server;

class Main extends PluginBase{

    /**
     * @var string[] $damagerList
     */
    private array $damagerList = [];
    /**
     * @var TaskHandler[] $closureList
     */
    private array $closureList = [];

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
        if(isset($this->closureList[$player->getName()])) $this->closureList[$player->getName()]->cancel();
        $this->closureList[$player->getName()] = $this->getScheduler()->scheduleDelayedTask(new ClosureTask(
            function () use ($player): void{
                $this->resetDamager($player);
            }
        ), 10 * 20);
    }
}