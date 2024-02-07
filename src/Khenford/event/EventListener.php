<?php

namespace Khenford\event;

use pocketmine\event\Listener;
use pocketmine\event\player\ {
    PlayerJoinEvent,
    PlayerInteractEvent
};

use Khenford\DonateCases;

class EventListener implements Listener {
    public function preLogin(PlayerJoinEvent $event): void {
        $player = $event->getPlayer();
        $username = $player->getPlayerInfo()->getUsername();
        DonateCases::getProviderManager()->setText($player, DonateCases::getProviderManager()->getProperty(DonateCases::$setting, "cases.text"));
        if (!DonateCases::getSqliteManager()->isUser($username)) {
            DonateCases::getSqliteManager()->addUserData($username, 0);
        }
    }

    public function onInteract(PlayerInteractEvent $event) {
        $player = $event->getPlayer();
        $username = $player->getPlayerInfo()->getUsername();
        $x = $event->getBlock()->getPosition()->getX(); $y = $event->getBlock()->getPosition()->getY(); $z = $event->getBlock()->getPosition()->getZ();
        if ($x == DonateCases::getProviderManager()->getProperty(DonateCases::$setting, "cases.x") and
            $y == DonateCases::getProviderManager()->getProperty(DonateCases::$setting, "cases.y") and
            $z == DonateCases::getProviderManager()->getProperty(DonateCases::$setting, "cases.z")) {
            if (DonateCases::getSqliteManager()->getKeys($username) > 0) {
                DonateCases::getProviderManager()->openCases($player, true);
                DonateCases::getSqliteManager()->removeKey($username, 1);
            } else {
                $player->sendMessage(DonateCases::getProviderManager()->getProperty(DonateCases::$setting, "messages.no-key"));
            }
        }
    }
}
