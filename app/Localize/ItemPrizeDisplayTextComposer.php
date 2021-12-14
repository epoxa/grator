<?php

namespace App\Localize;

use App\Model\Item;

interface ItemPrizeDisplayTextComposer
{
    function composeItemText(Item $item): string;
}