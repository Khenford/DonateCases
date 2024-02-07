<?php

namespace Khenford\provider;

use pocketmine\player\Player;
use pocketmine\world\particle\ {
    FlameParticle,
    HappyVillagerParticle,
    FloatingTextParticle
};
use pocketmine\utils\Config;
use pocketmine\math\Vector3;
use pocketmine\Server;

use Khenford\DonateCases;

use IvanCraft623\RankSystem\session\SessionManager;
use IvanCraft623\RankSystem\rank\RankManager;

class ProviderManager implements Provider {

    public function __construct() {}

    public static array $ranks;

    public function getNumberRanks(Player $player): int|string {
        foreach (SessionManager::getInstance()->get($player)->getRanks() as $rank) {
            if (isset(self::$ranks[strtolower($rank->getName())])) {
                return self::$ranks[strtolower($rank->getName())];
            }
        }
    }

    public function openCases(Player $player, bool $everyone): void {
        $randomRank = $this->randomRanks();
        $allRanks = RankManager::getInstance()->getAll();
        $count = 0;
        foreach ($allRanks as $rank => $value) {
            self::$ranks[$rank] = $count;
            $count++;
        }
        if ($this->getProperty(DonateCases::$setting, "effects")) {
            $this->makeSphere($player, 1);
            $this->spawnParticle();
        }
        if ($this->getNumberRanks($player) <= self::$ranks[$randomRank]) {
            $rank = RankManager::getInstance()->getRank($randomRank);
            SessionManager::getInstance()->get($player)->setRank($rank);
            if ($everyone) {
                Server::getInstance()->broadcastMessage(str_replace(["{username}", "{groups}"], [$player->getPlayerInfo()->getUsername(), $randomRank], DonateCases::getProviderManager()->getProperty(DonateCases::$setting, "messages.open.winner")));
            } else {
                $player->sendMessage(str_replace(["{groups}"], [$randomRank], DonateCases::getProviderManager()->getProperty(DonateCases::$setting, "messages.open.say-winner")));
            }
        } else {
            if ($everyone) {
                Server::getInstance()->broadcastMessage(str_replace(["{username}", "{groups}"], [$player->getPlayerInfo()->getUsername(), $randomRank], DonateCases::getProviderManager()->getProperty(DonateCases::$setting, "messages.open.no-luck")));
            } else {
                $player->sendMessage(str_replace(["{groups}"], [$randomRank], DonateCases::getProviderManager()->getProperty(DonateCases::$setting, "messages.open.say-no-luck")));
            }
        }
    }

    public function setText(Player $player, string $text): void {
        $particle = new FloatingTextParticle($text);
        $position = new Vector3($this->getProperty(DonateCases::$setting, "cases.x") + 0.5, $this->getProperty(DonateCases::$setting, "cases.y") + 1.5, $this->getProperty(DonateCases::$setting, "cases.z") + 0.5);
        $player->getWorld()->addParticle($position, $particle);
    }

    public function randomRanks(): string {
        $groups = DonateCases::getInstance()->getConfig()->getAll();
        $result = null;

        $totalChance = 0;
        foreach ($groups as $group => $value) {
            $totalChance += $this->getProperty(DonateCases::getInstance()->getConfig(), $group.".chance");
        }

        $random = rand(1, $totalChance);
        $currentChance = 0;

        foreach ($groups as $group => $value) {
            $chance = $this->getProperty(DonateCases::getInstance()->getConfig(), $group.".chance");
            $currentChance += $chance;

            if ($random <= $currentChance) {
                $result = $group;
                break;
            }
        }

        return $result;
    }

    private static function getDirection($pos) : Vector3
    {
        $y = -sin(deg2rad($pos->pitch));
        $xz = cos(deg2rad($pos->pitch));
        $x = -$xz * sin(deg2rad($pos->yaw));
        $z = $xz * cos(deg2rad($pos->yaw));
        return new Vector3($x, $y, $z);
    }

    private static function genCircle(int $radius, int $vertLines, int $dotsPerLine, $center) {
        $pitchDelta = (float)180 / $dotsPerLine;
        $yawDelta = (float)360 / $vertLines;

        $results = [];
        for ($i = 0; $i < $vertLines; ++$i) {
            for ($j = 0; $j < $dotsPerLine; ++$j) {
                $loc = clone($center);
                $loc->yaw = -180 + ($i * $yawDelta);
                $loc->pitch = -90 + ($j * $pitchDelta);
                $v = self::getDirection($loc)->normalize()->multiply($radius);
                $results[] = $v;
            }
        }
        return $results;
    }

    public function makeSphere(Player $player, int $radius = 3): void {
        $data = self::genCircle($radius, 4, 4, $player->getLocation());
        foreach ($data as $pos) Server::getInstance()->getWorldManager()->getWorldByName('world')->addParticle(new Vector3($this->getProperty(DonateCases::$setting, "cases.x") + $pos->getX() + 0.5, $this->getProperty(DonateCases::$setting, "cases.y") + $pos->getY() + 0.5, $this->getProperty(DonateCases::$setting, "cases.z") + $pos->getZ() + 0.5), new HappyVillagerParticle());
    }

    public function spawnParticle() {
        $world = Server::getInstance()->getWorldManager()->getWorldByName('world');
        $center = new Vector3($this->getProperty(DonateCases::$setting, "cases.x") + 0.5, $this->getProperty(DonateCases::$setting, "cases.y") + 0.5, $this->getProperty(DonateCases::$setting, "cases.z") + 0.5);
        $radius = 1.5; $step = 10;
        $particle = new FlameParticle($center);

        for ($i = 0; $i < 360; $i += $step) {
            $newX = $center->getX() + $radius * cos(deg2rad($i));
            $newZ = $center->getZ() + $radius * sin(deg2rad($i));
            $world->addParticle(new Vector3($newX, $center->getY(), $newZ), $particle);
        }
    }

    public function getProperty(Config $config, string $query) : string|int|bool {
        $keys = explode(".", $query);
        if (count($keys) == 1 && $keys[0] == $query) {
            return $config->get($keys[0]);
        } else {
            $data = [];
            foreach ($keys as $key) {
                if (empty($data)) {
                    $data = $config->get($key);
                } else {
                    $data = $data[$key];
                }
            }
        }
        return $data;
    }
}
