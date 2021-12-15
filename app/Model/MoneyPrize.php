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
        ?int $id = null,
    )
    {
        parent::__construct($id);
        $this->bean['money'] = $this->money;
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

    function accept(): void
    {
        Services::getDB()::exec('UPDATE bank SET total = total - ?, hold = hold - ?', [$this->money, $this->money]);
        $this->deactivateGame();
    }

    function decline(): void
    {
        Services::getDB()::exec('UPDATE item SET hold = hold - ?', [$this->money]);
        parent::decline();
    }

    function replaceToBonus(): void
    {
        $bonus = $this->money * Services::getConfig()['BONUS_PRIZE']['COEFFICIENT'];
        Services::getDB()::exec('UPDATE game SET money = null, bonus = ? WHERE id = ?', [$bonus, $this->bean['id']]);
        $this->deactivateGame();
    }
}