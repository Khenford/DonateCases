<?php

namespace Khenford\provider;

use pocketmine\player\Player;
use pocketmine\utils\Config;

interface Provider{
    
    public function getNumberRanks(Player $player): int|string;
    
    public function openCases(Player $player, bool $everyone): void;
    
    public function randomRanks(): string;
    
    public function getProperty(Config $config, string $query) : string|int|bool;
}
