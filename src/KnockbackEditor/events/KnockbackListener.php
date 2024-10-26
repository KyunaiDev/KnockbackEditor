<?php

namespace KnockbackEditor\events;

use pocketmine\event\Listener;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\player\Player;
use KnockbackEditor\Main;

class KnockbackListener implements Listener {
    private Main $plugin;
    private array $lastHitTime = []; // Store the last hit time for each player

    public function __construct(Main $plugin) {
        $this->plugin = $plugin;
    }

    public function onEntityDamageByEntity(EntityDamageByEntityEvent $event): void {
        $entity = $event->getEntity();
        $attacker = $event->getDamager();

        if (!$entity instanceof Player || !$attacker instanceof Player) {
            return;
        }
        $world = $entity->getWorld();
        $worldName = $world->getFolderName();
        $config = $this->plugin->getKnockbackConfig();
        if ($config->exists($worldName)) {
            $knockbackValues = $config->get($worldName);
            $attackDelay = intval($knockbackValues['attack-delay'] ?? 10);
            $currentTime = microtime(true);
            $playerId = $entity->getUniqueId()->toString();
            if (isset($this->lastHitTime[$playerId])) {
                $lastHit = $this->lastHitTime[$playerId];
                $elapsedTicks = ($currentTime - $lastHit) * 20;
                if ($elapsedTicks < $attackDelay) {
                    $event->cancel(); 
                    return;
                }
            }
            $this->lastHitTime[$playerId] = $currentTime;
            $event->setKnockBack(floatval($knockbackValues["x"]), floatval($knockbackValues["y"]), floatval($knockbackValues["z"]));
        }
    }
}