<?php

namespace App\Method;

use App\Model\User;

class PrizeDeclineCommand  implements UserCommand
{

    function getCommandName(): string
    {
        return 'decline';
    }

    /**
     * @throws InvalidStateException
     */
    function execute(User $user): void
    {
        $game = $user->getCurrentGame();
        if (!$game) throw new InvalidStateException('Game not started');
        $game->decline();
        $user->setCurrentGame(null);
    }
}