<?php

namespace App\Method;

use App\Model\GameModel;
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
        Services::getDB()::transaction(function () use ($user) {
            $this->checkGameNotStarted($user);
            GameRepository::createNewRandomPrizeGame($user);
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