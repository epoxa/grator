<?php

namespace App\Method;

use App\Model\User;

class PrizeAcceptCommand extends ServicesAwareMethod implements UserCommand
{

    function getCommandName(): string
    {
        return 'accept';
    }

    function execute(User $user): void
    {
        //
    }
}