<?php

namespace App\Method;

use App\Model\User;
use App\Service\Services;

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
        Services::getLog()->info($user->getUsername() . " declines the prize");
        $game->decline();
        $user->setCurrentGame(null);
    }
}