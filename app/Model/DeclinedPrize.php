<?php

namespace App\Model;

use App\Localize\UITranslator;
use App\Method\InvalidStateException;

class DeclinedPrize extends AbstractGame implements Game
{

    function getOfferText(UITranslator $translator): string
    {
        return ''; // No need to call in the set domain
    }

    function getAvailableCommands(): iterable
    {
        return [
        ];
    }

    /**
     * @throws InvalidStateException
     */
    function accept(): void
    {
        throw new InvalidStateException('Game declined');
    }

    /**
     * @throws InvalidStateException
     */
    function decline(): void
    {
        throw new InvalidStateException('Game is already declined');
    }

}