<?php

namespace App\Method;

use App\Model\User;

class PrizeAcceptCommand implements UserCommand
{

    function getCommandName(): string
    {
        return 'accept';
    }

    /**
     * @throws InvalidStateException
     */
    function execute(User $user): void
    {
        $game = $user->getCurrentGame();
        if (!$game) throw new InvalidStateException(InvalidStateException::GAME_NOT_STARTED);
        $game->accept();
        $user->setCurrentGame(null);
    }
}