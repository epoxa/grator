<?php

namespace App\Web;

use App\Localize\BonusPrizeDisplayTextComposer;
use App\Localize\GameWelcomeDisplayTextComposer;
use App\Localize\ItemPrizeDisplayTextComposer;
use App\Localize\MoneyPrizeDisplayTextComposer;
use App\Localize\UserCommandDisplayTextComposer;
use App\Model\Item;

class WebTranslator implements
    GameWelcomeDisplayTextComposer,
    MoneyPrizeDisplayTextComposer, BonusPrizeDisplayTextComposer, ItemPrizeDisplayTextComposer,
    UserCommandDisplayTextComposer
{

    public function __construct(
        private array $appConfig
    )
    {
    }

    const COMMAND_WEB_NAMES = [
        'start' => 'Start Game',
        'accept' => 'Accept This Prize',
        'change' => 'Select Slotebonuses',
        'reject' => 'No Thank You',
    ];

    function composeWelcomeText(int $moneyBankFund, int $itemsTotalCount): string
    {
        return "Welcome to the game! Start game to win one of the $itemsTotalCount items,"
            . " money from $moneyBankFund $ fund, or unlimited slotebonuses!";
    }

    function composeMoneyText(int $money): string
    {
        $rate = $this->appConfig['BONUS']['COEFFICIENT'];
        $formattedMoney = number_format($money / 100, 2, '', '');
        $bonus = $money * $rate;
        $formattedBonus = number_format($bonus / 100, 2, '', '');;
        return
            "You won $formattedMoney dollars! Congrats!"
            . " But you can select $formattedBonus slotebonuses instead."
            . " What do ypu choose?";
    }

    function composeBonusText(int $bonus): string
    {
        $formatted = number_format($bonus / 100, 2, '', '');
        return "You won $formatted slotebonuses! Incredible!";
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

    function composeCommandCaption(string $commandName): string
    {
        return static::COMMAND_WEB_NAMES[$commandName] ?? $commandName; // Just in case
    }

}