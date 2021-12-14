<?php

namespace App\Model;

use App\Method\UserCommand;

interface Offer
{

    function getText(): string;

    /**
     * @return UserCommand[]
     */
    function getMethods(): Iterable;
}