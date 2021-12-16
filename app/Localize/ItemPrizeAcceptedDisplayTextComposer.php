<?php

namespace App\Localize;

use App\Model\Item;

interface ItemPrizeAcceptedDisplayTextComposer
{
    function composeItemSendText(Item $item): string;
}