<?php

namespace App\Method;

use App\Service\ServiceLocator;

class ServicesAwareMethod
{
    public function __construct(
        protected ServiceLocator $services
    )
    {
    }
}