<?php

namespace App\Method;

use App\Model\BonusPrize;
use App\Model\Game;
use App\Model\ItemPrize;
use App\Model\MoneyPrize;
use App\Model\User;
use App\Service\Services;
use Error;

class SendPrizeCommand implements SendPrize
{

    function send(Game $game)
    {
        $user = $game->getUser();
        if ($game instanceof BonusPrize) {
            $this->processBonus($user, $game);
        } else if ($game instanceof MoneyPrize) {
            $this->processMoney($user, $game);
        } else if ($game instanceof ItemPrize) {
            $this->processItem($user, $game);
        } else {
            throw new Error('Unknown prize kind');
        }
        $game->markProcessed();
    }

    function processBonus(User $user, BonusPrize $game)
    {
        Services::getBonusProcessor()->topUpBonuses($user->getId(), $game->getBonus());
    }

    function processMoney(User $user, MoneyPrize $game)
    {
        Services::getBankProcessor()->processPayment($user->getCardNumber(), $game->getMoney());
    }

    function processItem(User $user, ItemPrize $game)
    {
        Services::getItemProcessor()->notifyStaff($user->getUsername(), $user->getPostAddress(), $game->getItem()->getName());
    }

}