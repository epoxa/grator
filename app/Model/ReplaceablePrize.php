<?php

namespace App\Model;

use App\Localize\UITranslator;

interface ReplaceablePrize
{
    function replaceToBonus(UITranslator $translator): string;
}