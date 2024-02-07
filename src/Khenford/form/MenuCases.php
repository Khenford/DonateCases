<?php

namespace Khenford\form;

use pocketmine\player\Player;

use Khenford\DonateCases;
use Khenford\form\buttons\OpenCases;
use Khenford\form\buttons\Info;
use Khenford\form\buttons\TransferKey;

use com\formconstructor\form\element\simple\Button;
use com\formconstructor\form\element\simple\ImageType;
use com\formconstructor\form\SimpleForm;

class MenuCases{
    
    public function __construct(Player $player){
        $form = new SimpleForm(DonateCases::getProviderManager()->getProperty(DonateCases::$setting, "forms.menu.title"));
        $form->addContent(str_replace(["{count}"], [DonateCases::getSqliteManager()->getKeys($player->getPlayerInfo()->getUsername())], DonateCases::getProviderManager()->getProperty(DonateCases::$setting, "forms.menu.text")));
        $form->addButton((new Button(DonateCases::getProviderManager()->getProperty(DonateCases::$setting, "buttons.menu.open")))
            ->onClick(function() use ($player){
                    if(DonateCases::getSqliteManager()->getKeys($player->getPlayerInfo()->getUsername()) <= 0){
                        $player->sendMessage(DonateCases::getProviderManager()->getProperty(DonateCases::$setting, "messages.no-key"));
                        return;
                    }
                    new OpenCases($player);
            }))
            ->addButton((new Button(DonateCases::getProviderManager()->getProperty(DonateCases::$setting, "buttons.menu.transfer")))
                ->onClick(function() use ($player){
                    if(DonateCases::getSqliteManager()->getKeys($player->getPlayerInfo()->getUsername()) <= 0){
                        $player->sendMessage(DonateCases::getProviderManager()->getProperty(DonateCases::$setting, "messages.no-key"));
                        return;
                    }
                    new TransferKey($player);
            }));
        $form->send($player);
    }
}
