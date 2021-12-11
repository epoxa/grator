<?php

namespace App\Method;

use App\Model\User;

interface Status
{
    function get(User $user): array;
}