<?php

namespace App\Method;

use App\Model\User;

class StatusQuery extends Method implements Status
{
    function get(User $user): array
    {
        return [];
    }
}