<?php

namespace Khenford\form\buttons;

use pocketmine\player\Player;
use pocketmine\Server;

use Khenford\database\SQLiteData;
use Khenford\DonateCases;

use com\formconstructor\form\element\custom\Dropdown;
use com\formconstructor\form\element\custom\Slider;
use com\formconstructor\form\response\CustomFormResponse;
use com\formconstructor\form\CustomForm;

class TransferKey{
    
    public function __construct(Player $player){
        $counts = DonateCases::getSqliteManager()->getKeys($player->getPlayerInfo()->getUsername());
        $form = new CustomForm(DonateCases::getProviderManager()->getProperty(DonateCases::$setting, "forms.transfer.title"));
        $form->addElement("count", (new Slider(DonateCases::getProviderManager()->getProperty(DonateCases::$setting, "buttons.transfer.slider-count"), 1, $counts, 1, 1)));
        $dropdown =  (new Dropdown(DonateCases::getProviderManager()->getProperty(DonateCases::$setting, "buttons.transfer.transfer-player")));
        foreach (Server::getInstance()->getOnlinePlayers() as $players){
            $dropdown->addText($players->getPlayerInfo()->getUsername());
        }
        $form->addElement("username", $dropdown);
            
        $form->setHandler(function(Player $player, CustomFormResponse $response){
            $count = $response->getSlider("count")->getValue();
            $username = $response->getDropdown("username")->getName();
            DonateCases::getSqliteManager()->TransferKey($player->getPlayerInfo()->getUsername(), $username, $count);
            $player->sendMessage(str_replace(["{count}", "{username}"], [$count, $username], DonateCases::getProviderManager()->getProperty(DonateCases::$setting, "messages.transfer.success")));
            $toPlayer = Server::getInstance()->getPlayerExact($username);
            if($toPlayer !== null){
                $toPlayer->sendMessage(str_replace(["{count}", "{username}"], [$count, $player->getPlayerInfo()->getUsername(), DonateCases::getProviderManager()->getProperty(DonateCases::$setting, "messages.transfer.player")]));
            }
        });
        $form->send($player);
    }
}
