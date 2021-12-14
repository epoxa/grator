<?php

namespace App\Localize;

interface DisplayTextComposer
{
    function selectImplementation(
        MoneyPrizeDisplayTextComposer $money,
        BonusPrizeDisplayTextComposer $bonus,
        ItemPrizeDisplayTextComposer $item,
    ): string;
}