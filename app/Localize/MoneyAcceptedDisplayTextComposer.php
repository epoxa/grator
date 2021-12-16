<?php

namespace App\Localize;

interface MoneyAcceptedDisplayTextComposer
{
    function composeMoneyPayText(int $money): string;
}