<?php

namespace App\Method;

use App\Model\Offer;
use App\Model\User;

interface StatusQuery
{
    function get(User $user): Offer;
}