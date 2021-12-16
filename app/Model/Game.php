<?php

namespace App\Model;

use App\Localize\UITranslator;

interface Game
{
    function getId(): int;
    function getOfferText(UITranslator $translator): string;
    function getAvailableCommands(): Iterable;
    function accept(UITranslator $translator): string;
    function decline(): void;
}