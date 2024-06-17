<?php

declare(strict_types=1);

namespace banira4649\BetterKillJudgement\event;

use banira4649\BetterKillJudgement\Main;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\player\Player;
use pocketmine\scheduler\ClosureTask;

class EventListener implements Listener{

    private Main $main;

    public function __construct(Main $main){
        $this->main = $main;
    }

    public function onPlayerQuitEvent(PlayerQuitEvent $event): void{
        $this->main->resetDamager($event->getPlayer());
    }

    public function onEntityDamageByEntityEvent(EntityDamageByEntityEvent $event): void{
        $entity = $event->getEntity();
        $damager = $event->getDamager();
        if(!$entity instanceof Player || !$damager instanceof Player) return;
        if($entity->isAlive()){
            $this->main->setDamager($entity, $damager);
        }
    }

    public function onEntityDamageEvent(EntityDamageEvent $event): void{
        $entity = $event->getEntity();
        if(!$entity instanceof Player) return;
        if(!$event instanceof EntityDamageByEntityEvent){
            if(($damager = $this->main->getDamager($entity)) !== null){
                if($entity->isAlive() && $entity->getHealth() - $event->getFinalDamage() <= 0){
                    $event->cancel();
                    $this->main->resetDamager($entity);
                    (new EntityDamageByEntityEvent($damager, $entity, EntityDamageEvent::CAUSE_ENTITY_ATTACK, $event->getFinalDamage()))->call();
                    if(!class_exists("\NeiroNetwork\AlternativeCoreWars\Main")){
                        if($event->getCause() === EntityDamageEvent::CAUSE_VOID){
                            $pos = $entity->getPosition()->add(0, $entity->getFallDistance(), 0);
                            Main::getInstance()->getScheduler()->scheduleDelayedTask(
                                new ClosureTask(fn() => $entity->isOnline() && $entity->teleport($pos)),
                                4
                            );
                        }
                    }
                }
            }
        }
    }
}