<?php

namespace App\Model;

use App\Localize\UITranslator;
use App\Method\PrizeAcceptCommand;
use App\Method\PrizeDeclineCommand;
use App\Method\PrizeReplaceBonusesCommand;
use App\Service\Services;

class MoneyPrize extends AbstractGame implements Game, ReplaceablePrize
{

    public function __construct(
        private ?int $money,
        ?int         $gameId = null,
    )
    {
        parent::__construct($gameId);
        if ($this->money) {
            $this->bean['money'] = $this->money;
        } else {
            $this->money = $this->bean['money'];
        }
    }

    function getOfferText(UITranslator $translator): string
    {
        return $translator->composeMoneyText($this->money);
    }

    function getAvailableCommands(): iterable
    {
        return [
            new PrizeAcceptCommand(),
            new PrizeReplaceBonusesCommand(),
            new PrizeDeclineCommand(),
        ];
    }

    function accept(UITranslator $translator): string
    {
        Services::getDB()::exec('UPDATE bank SET total = total - ?, hold = hold - ?', [$this->money, $this->money]);
        $this->deactivateGame();
        return $translator->composeMoneyPayText($this->money);
    }

    function decline(): void
    {
        Services::getDB()::exec('UPDATE bank SET hold = hold - ?', [$this->money]);
        parent::decline();
    }

    function replaceToBonus(UITranslator $translator): string
    {
        $bonus = $this->money * Services::getConfig()['BONUS_PRIZE']['COEFFICIENT'];
        Services::getDB()::exec('UPDATE game SET money = null, bonus = ? WHERE id = ?', [$bonus, $this->bean['id']]);
        Services::getDB()::exec('UPDATE bank SET hold = hold - ?', [$this->money]);
        $this->deactivateGame();
        return $translator->composeBonusAcceptedText($bonus);
    }
}