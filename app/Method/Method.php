<?php

namespace App\Method;

use App\Service\ServiceLocator;

class Method
{
    public function __construct(
        protected ServiceLocator $services
    )
    {
    }
}