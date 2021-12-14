<?php

namespace App\Method;

use App\Model\User;

class StartGameCommand extends ServicesAwareMethod implements UserCommand
{

    function getCommandName(): string
    {
        return 'start';
    }

    /**
     * @throws InvalidStateException
     */
    function execute(User $user): void
    {
        $this->checkGameNotStarted($user);
        $db = $this->services->getDB();
        $db::transaction(function () use ($user) {
            $this->checkGameNotStarted($user); // Yes again!

        });
    }

    /**
     * @param User $user
     * @throws InvalidStateException
     */
    private function checkGameNotStarted(User $user): void
    {
        $game = $user->getCurrentGame();
        if ($game) {
            throw new InvalidStateException('Game already started');
        }
    }
}