<?php

namespace App\Method;

use App\Model\User;

class PrizeReplaceBonusesCommand extends ServicesAwareMethod implements UserCommand
{

    function getCommandName(): string
    {
        return 'replace';
    }

    function execute(User $user): void
    {
        //
    }
}