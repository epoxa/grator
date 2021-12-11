<?php

namespace App\Method;

use App\Service\ServiceLocator;

interface DebugReset
{
    function execute(ServiceLocator $services): void;
}