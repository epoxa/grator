<?php

namespace App\Method;

use App\Model\User;

interface UserCommand
{
    function getCommandName(): string;
    function execute(User $user): void;
}