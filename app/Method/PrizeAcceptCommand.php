<?php

namespace App\Method;

use App\Model\User;
use App\Web\WebTranslator;

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
        $message = $game->accept(new WebTranslator($user));
        $user->setCurrentGame(null);
        $user->sendMessage($message);
        $game->scheduleProcessing();
    }
}