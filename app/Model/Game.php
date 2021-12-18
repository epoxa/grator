<?php

namespace App\Model;

use App\Localize\UITranslator;

interface Game
{
    function getId(): int;
    function getUser(): User;
    function getOfferText(UITranslator $translator): string;
    function getAvailableCommands(): Iterable;
    function accept(UITranslator $translator): string;
    function decline(): void;
    function scheduleProcessing();
    function markProcessed();
}