<?php

namespace App\Model;

use App\Localize\UITranslator;
use App\Service\DummyBonusProcessor;
use App\Service\Services;

class BonusPrizeModel extends AbstractGame implements Game, BonusPrize
{

    public function __construct(
        private ?int $bonus,
        ?int         $gameId = null,
    )
    {
        parent::__construct($gameId);
        if ($this->bonus) {
            $this->bean['bonus'] = $this->bonus;
        } else {
            $this->bonus = $this->bean['bonus'];
        }
    }

    function getOfferText(UITranslator $translator): string
    {
        return $translator->composeBonusText($this->bonus);
    }

    function accept(UITranslator $translator): string
    {
        parent::deactivateGame();
        return $translator->composeBonusAcceptedText($this->bonus);
    }

    function getBonus(): int
    {
        return $this->bonus;
    }
}