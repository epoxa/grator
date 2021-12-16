<?php

namespace App\Web;

use App\Localize\BonusPrizeDisplayTextComposer;
use App\Localize\GameWelcomeDisplayTextComposer;
use App\Localize\ItemPrizeDisplayTextComposer;
use App\Localize\MoneyPrizeDisplayTextComposer;
use App\Localize\UITranslator;
use App\Localize\UserCommandDisplayTextComposer;
use App\Model\Item;
use App\Model\User;
use App\Service\Services;

class WebTranslator implements UITranslator
{

    public function __construct(
        private User $user
    )
    {
    }

    function composeWelcomeText(int $moneyBankFund, int $itemsTotalCount): string
    {
        $userName = $this->user->getUsername();
        $text = "Welcome to the game, $userName!<br> Start game to win ";
        if ($itemsTotalCount) {
            $text .= "one of the <em class='count'>$itemsTotalCount</em> items, ";
        }
        if ($moneyBankFund > Services::getConfig()['MONEY_PRIZE']['MIN']) {
            $money = ceil($moneyBankFund / 100);
            $text .= "money from <em class='money'>$money $</em> fund, ";
        }
        if ($itemsTotalCount || $moneyBankFund) {
            $text .= "or ";
        }
        $text .= "unlimited slotebonuses!";
        return $text;
    }

    function composeMoneyText(int $money): string
    {
        $rate = Services::getConfig()['BONUS_PRIZE']['COEFFICIENT'];
        $formattedMoney = number_format($money / 100, 2, '.', '');
        $bonus = $money * $rate;
        return
            "You won <em class='money'>$formattedMoney</em> dollars! Congrats!<br>"
            . " But you can select <em class='bonus'>$bonus</em> slotebonuses instead.<br>"
            . " What is you choice?";
    }

    function composeBonusText(int $bonus): string
    {
        return "You won <em class='bonus'>$bonus</em> slotebonuses! Incredible!";
    }

    function composeItemText(Item $item): string
    {
        $rest = $item->fundRest();
        $itemName = $item->getName();
        if ($rest === 1) {
            return "You won the last <em class='item'>$itemName</em>! You are so lucky!";
        } else {
            return "<em class='item'>$itemName</em>. You won one of the $rest items! Not bad, c'mon!";
        }
    }

    private const COMMAND_WEB_NAMES = [
        'start' => 'Start Game',
        'accept' => 'Accept This Prize',
        'replace' => 'Select Slotebonuses',
        'decline' => 'No Thank You',
    ];

    function composeCommandCaption(string $commandName): string
    {
        return static::COMMAND_WEB_NAMES[$commandName] ?? $commandName; // Just in case
    }

    function composeItemSendText(Item $item): string
    {
        $itemName = $item->getName();
        return "$itemName will be sent to you in the mail as soon as possible!";
    }

    function composeMoneyPayText(int $money): string
    {
        $formatted = number_format($money / 100, 2, '.', '');
        return "$formatted $ will be transferred to you credit card soon";
    }

    function composeBonusAcceptedText(int $bonus): string
    {
        return "You got $bonus slotebonuses";
    }
}