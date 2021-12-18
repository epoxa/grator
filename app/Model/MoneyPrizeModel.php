<?php

namespace App\Model;

use App\Localize\UITranslator;
use App\Method\PrizeAcceptCommand;
use App\Method\PrizeDeclineCommand;
use App\Method\PrizeReplaceBonusesCommand;
use App\Service\Services;

class MoneyPrizeModel extends AbstractGame implements Game, MoneyPrize, ReplaceablePrize
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
        Services::getDB()::exec('UPDATE bank SET total = total - ?, hold = hold - ?', [$this->getMoney(), $this->money]);
        $this->deactivateGame();
        return $translator->composeMoneyPayText($this->money);
    }

    function decline(): void
    {
        Services::getDB()::exec('UPDATE bank SET hold = hold - ?', [$this->getMoney()]);
        parent::decline();
    }

    function replaceToBonus(UITranslator $translator): string
    {
        $bonus = $this->money * Services::getConfig()['BONUS_PRIZE']['COEFFICIENT'];
        Services::getDB()::exec('UPDATE game SET money = null, bonus = ? WHERE id = ?', [$bonus, $this->getId()]);
        Services::getDB()::exec('UPDATE bank SET hold = hold - ?', [$this->getMoney()]);
        $this->deactivateGame();
        return $translator->composeBonusAcceptedText($bonus);
    }

    function getMoney(): int
    {
        return $this->money;
    }
}