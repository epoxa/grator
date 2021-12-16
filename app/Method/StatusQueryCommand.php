<?php

namespace App\Method;

use App\Localize\BonusPrizeDisplayTextComposer;
use App\Localize\GameWelcomeDisplayTextComposer;
use App\Localize\ItemPrizeDisplayTextComposer;
use App\Localize\MoneyPrizeDisplayTextComposer;
use App\Localize\UITranslator;
use App\Localize\UserCommandDisplayTextComposer;
use App\Model\Offer;
use App\Model\OfferModel;
use App\Model\User;
use App\Service\ServiceLocator;
use App\Service\Services;

class StatusQueryCommand implements StatusQuery
{
    function __construct(
        private UITranslator $translator,
    )
    {
    }

    function get(User $user): Offer
    {
        $db = Services::getDB();
        $game = $user->getCurrentGame();
        if ($game) {
            return new OfferModel(
                $game->getOfferText($this->translator),
                $game->getAvailableCommands()
            );
        } else {
            $itemsCount = $db::getCell('SELECT SUM(`count` - hold) FROM item');
            $moneyAmount = $db::getCell('SELECT total - hold FROM bank');
            return new OfferModel(
                $this->translator->composeWelcomeText($moneyAmount, $itemsCount),
                [
                    new StartGameCommand(),
                ]
            );
        }
    }
}