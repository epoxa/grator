<?php

namespace App\Method;

use App\Localize\BonusPrizeDisplayTextComposer;
use App\Localize\GameWelcomeDisplayTextComposer;
use App\Localize\ItemPrizeDisplayTextComposer;
use App\Localize\MoneyPrizeDisplayTextComposer;
use App\Localize\UserCommandDisplayTextComposer;
use App\Model\Offer;
use App\Model\OfferModel;
use App\Model\User;
use App\Service\ServiceLocator;

class StatusQueryCommand extends ServicesAwareMethod implements StatusQuery
{
    function __construct(
        ServiceLocator $services,
        private GameWelcomeDisplayTextComposer $welcomeComposer,
        private MoneyPrizeDisplayTextComposer $moneyComposer,
        private BonusPrizeDisplayTextComposer $bonusComposer,
        private ItemPrizeDisplayTextComposer $itemComposer,
        private UserCommandDisplayTextComposer $commandComposer,
    )
    {
        parent::__construct($services);
    }

    function get(User $user): Offer
    {
        $db = $this->services->getDB();
        $game = $user->getCurrentGame();
        if ($game) {

        } else {
//            $db::getCell('SELECT SUM(`count`) FROM item, ')
            return new OfferModel(
                $this->welcomeComposer->composeWelcomeText(100,100),
                [
                    new StartGameCommand($this->services),
                ]
            );
        }
    }
}