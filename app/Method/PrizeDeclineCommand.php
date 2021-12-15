<?php

namespace App\Method;

use App\Model\User;

class PrizeDeclineCommand  implements UserCommand
{

    function getCommandName(): string
    {
        return 'decline';
    }

    function execute(User $user): void
    {
        //
    }
}