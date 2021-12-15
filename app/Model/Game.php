<?php

namespace App\Model;

use App\Localize\UITranslator;

interface Game
{
    function getOfferText(UITranslator $translator): string;
    function getAvailableCommands(): Iterable;
    function accept(): void;
    function decline(): void;
}