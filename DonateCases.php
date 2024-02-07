<?php

namespace Khenford;

use pocketmine\plugin\PluginBase;
use pocketmine\player\Player;
use pocketmine\utils\Config;

use Khenford\event\EventListener;
use Khenford\command\CasesCommand;
use Khenford\database\SQLiteDataManager;
use Khenford\database\SQLiteData;
use Khenford\provider\Provider;
use Khenford\provider\ProviderManager;

class DonateCases extends PluginBase {

    public static SQLiteData $sqlite;
    public static Config $setting;
    public static Provider $provider;
    public static ?DonateCases $instance = null;
    
    public function onEnable(): void{
        $this->getServer()->getCommandMap()->register("", new CasesCommand());
        $this->getServer()->getPluginManager()->registerEvents(new EventListener(), $this);
    }

    public function onLoad(): void {
        $this->loadDatabase();
        $this->loadConfig();
    }
    
    public function loadConfig(): void {
        self::$instance = $this;
        self::$provider = new ProviderManager();
        $this->saveDefaultConfig();
        $this->saveResource("setting.yml");
        self::$setting = new Config($this->getDataFolder()."setting.yml", Config::YAML);
    }

    public function loadDatabase(): void {
        self::$sqlite = new SQLiteDataManager($this->getDataFolder()."users.db");
    }

    public static function getInstance(): ?DonateCases {
        return self::$instance;
    }
    
    public static function getSqliteManager(): SQLiteData {
        return self::$sqlite;
    }
    
    public static function getProviderManager(): Provider{
        return self::$provider;
    }
}