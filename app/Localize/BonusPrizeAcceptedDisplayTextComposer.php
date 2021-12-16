<?php

namespace App\Localize;

interface BonusPrizeAcceptedDisplayTextComposer
{
    function composeBonusAcceptedText(int $bonus): string;
}