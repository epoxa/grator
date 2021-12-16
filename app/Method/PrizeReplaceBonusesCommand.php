<?php

namespace App\Method;

use App\Model\ReplaceablePrize;
use App\Model\User;

class PrizeReplaceBonusesCommand implements UserCommand
{

    function getCommandName(): string
    {
        return 'replace';
    }

    /**
     * @throws InvalidStateException
     */
    function execute(User $user): void
    {
        $game = $user->getCurrentGame();
        if (!$game) throw new InvalidStateException(InvalidStateException::GAME_NOT_STARTED);
        if (!$game instanceof ReplaceablePrize) throw new InvalidStateException("Can not replace the prize");
        $game->replaceToBonus();
        $user->setCurrentGame(null);
    }
}