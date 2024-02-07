<?php

namespace Khenford\form\buttons;

use pocketmine\player\Player;

use Khenford\database\SQLiteData;
use Khenford\DonateCases;

use com\formconstructor\form\element\custom\Toggle;
use com\formconstructor\form\response\CustomFormResponse;
use com\formconstructor\form\CustomForm;

class OpenCases{
    
    public function __construct(Player $player){
        $count = DonateCases::getSqliteManager()->getKeys($player->getPlayerInfo()->getUsername());
        $form = new CustomForm(DonateCases::getProviderManager()->getProperty(DonateCases::$setting, "forms.open.title"));
        $form->addElement("everyone", (new Toggle(DonateCases::getProviderManager()->getProperty(DonateCases::$setting, "buttons.open.everyone"), true)));
            
        $form->setHandler(function(Player $player, CustomFormResponse $response){
            $everyone = $response->getToggle("everyone")->getValue();
            DonateCases::getProviderManager()->openCases($player, $everyone);
            DonateCases::getSqliteManager()->removeKey($player->getPlayerInfo()->getUsername(), 1);
        });
        $form->send($player);
    }
}