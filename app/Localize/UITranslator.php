<?php

namespace App\Localize;

interface UITranslator extends
    GameWelcomeDisplayTextComposer,
    BonusPrizeDisplayTextComposer,
    MoneyPrizeDisplayTextComposer,
    ItemPrizeDisplayTextComposer,
    UserCommandDisplayTextComposer,
    MoneyAcceptedDisplayTextComposer,
    ItemPrizeAcceptedDisplayTextComposer,
    BonusPrizeAcceptedDisplayTextComposer
{
}