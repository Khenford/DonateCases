<?php

namespace Khenford\command;

use pocketmine\command\ {
    Command,
    CommandSender
};
use pocketmine\player\Player;

use Khenford\form\MenuCases;
use Khenford\DonateCases;

class CasesCommand extends Command {
    public function __construct() {
        parent::__construct("donatecases", "open UI Donate Cases");
        $this->setPermission("donatecases.command");
        $this->setAliases(["dc"]);
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args): void {
        if (!($sender instanceof Player)) {
            if (count($args) > 2) {
                switch ($args[0]) {
                    case "add":
                        if (DonateCases::getSqliteManager()->isUser($args[1])) {
                            if (is_numeric($args[2])) {
                                DonateCases::getSqliteManager()->addKey($args[1], $args[2]);
                            } else {
                                \GlobalLogger::get()->info(DonateCases::getProviderManager()->getProperty(DonateCases::$setting, "messages.command.no-integer"));
                            }
                        } else {
                            \GlobalLogger::get()->info(str_replace(["{name}"], [$args[1]], DonateCases::getProviderManager()->getProperty(DonateCases::$setting, "messages.command.no-database")));
                        }
                        break;
                    case "remove":
                        if (DonateCases::getSqliteManager()->isUser($args[1])) {
                            if (is_numeric($args[2])) {
                                DonateCases::getSqliteManager()->removeKey($args[1], $args[2]);
                            } else {
                                \GlobalLogger::get()->info(DonateCases::getProviderManager()->getProperty(DonateCases::$setting, "messages.command.no-integer"));
                            }
                        } else {
                            \GlobalLogger::get()->info(str_replace(["{name}"], [$args[1]], DonateCases::getProviderManager()->getProperty(DonateCases::$setting, "messages.command.no-database")));
                        }
                        break;
                }
            } else {
                \GlobalLogger::get()->info(DonateCases::getProviderManager()->getProperty(DonateCases::$setting, "messages.command.usage"));
            }
        } else {
            new MenuCases($sender);
        }
    }
}
