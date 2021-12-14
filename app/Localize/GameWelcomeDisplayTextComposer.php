<?php

namespace App\Localize;

interface GameWelcomeDisplayTextComposer
{
    function composeWelcomeText(int $moneyBankFund, int $itemsTotalCount): string;
}