<?php

namespace App\Model;

use App\Localize\UITranslator;

class BonusPrize extends AbstractGame implements Game
{

    public function __construct(
        private ?int $bonus,
        ?int         $id = null,
    )
    {
        parent::__construct($id);
        $this->bean['bonus'] = $this->bonus;
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