<?php

namespace App\Method;

use App\Model\GameRepository;
use App\Model\User;
use App\Service\Services;

class StartGameCommand implements UserCommand
{

    function getCommandName(): string
    {
        return 'start';
    }

    function execute(User $user): void
    {
        Services::getLog()->info($user->getUsername() . " starts game");
        $newGame = Services::getDB()::transaction(function () use ($user) {
            $this->checkGameNotStarted($user);
            return GameRepository::createNewRandomPrizeGame($user);
        });
        $user->setCurrentGame($newGame);
    }

    /**
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