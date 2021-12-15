<?php

namespace App\Service;

use Psr\Log\LoggerInterface;
use RedBeanPHP\Facade;

interface ServiceLocator
{
    static function getConfig(): array;

    static function getLog(): LoggerInterface;

    static function getDB(): Facade;

}