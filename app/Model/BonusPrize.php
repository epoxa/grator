<?php

namespace App\Model;

use App\Localize\UITranslator;

class BonusPrize extends AbstractGame implements Game
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

    function accept(): void
    {
        parent::deactivateGame();
    }
}