<?php

namespace App\Localize;

interface BonusPrizeDisplayTextComposer
{
    function composeBonusText(int $bonus): string;
}