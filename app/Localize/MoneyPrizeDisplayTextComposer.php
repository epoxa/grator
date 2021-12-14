<?php

namespace App\Localize;

interface MoneyPrizeDisplayTextComposer
{
    function composeMoneyText(int $money): string;
}