<?php

namespace App\Service;

use Psr\Log\LoggerInterface;
use RedBeanPHP\Facade;

interface ServiceLocator
{
    function getConfig(): array;

    function getLog(): LoggerInterface;

    function getDB(): Facade;

}