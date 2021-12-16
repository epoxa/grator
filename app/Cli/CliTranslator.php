<?php

namespace App\Cli;

use App\Localize\UITranslator;
use App\Model\Item;
use App\Model\User;
use App\Service\Services;

class CliTranslator implements UITranslator
{

    public function __construct(
        private User $user
    )
    {
    }

    function composeWelcomeText(int $moneyBankFund, int $itemsTotalCount): string
    {
        $userName = $this->user->getUsername();
        $text = "Welcome to the game, $userName!\nWe have next prizes:\n\n";
        if ($itemsTotalCount) {
            $text .= "       Items: $itemsTotalCount\n";
        }
        if ($moneyBankFund > Services::getConfig()['MONEY_PRIZE']['MIN']) {
            $money = ceil($moneyBankFund / 100);
            $text .= "       Money: $money $\n";
        }
        if ($itemsTotalCount || $moneyBankFund) {
            $text .= "Slotebonuses: infinite\n";
        }
        $text .= "\nPress (s) to start game";
        return $text;
    }

    function composeMoneyText(int $money): string
    {
        $rate = Services::getConfig()['BONUS_PRIZE']['COEFFICIENT'];
        $formattedMoney = number_format($money / 100, 2, '.', '');
        $bonus = $money * $rate;
        return
            "You won $formattedMoney dollars! Congrats!\n"
            . "But you can select $bonus slotebonuses instead.\n"
            . "What is you choice?";
    }

    function composeBonusText(int $bonus): string
    {
        return "You won $bonus slotebonuses! Incredible!";
    }

    function composeItemText(Item $item): string
    {
        $rest = $item->fundRest();
        $itemName = $item->getName();
        if ($rest === 1) {
            return "You won the last $itemName! You are so lucky!";
        } else {
            return "$itemName. You won one of the $rest items! Not bad, c'mon!";
        }
    }

    private const COMMAND_WEB_NAMES = [
        'start' => '(s)tart',
        'accept' => '(a)ccept',
        'replace' => '(c)hange',
        'decline' => '(d)ecline',
        'reset' => '(r)eset',
        'exit' => 'e(x)it',
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