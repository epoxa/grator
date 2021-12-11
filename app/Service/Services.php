<?php

namespace App\Service;

use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Psr\Log\LoggerInterface;
use RedBeanPHP\Facade;

class Services implements ServiceLocator
{

    const APP_NAME = 'eGrator';

    static private ?LoggerInterface $logger = null;
    static private ?Facade $db = null;

    function getConfig(): array
    {
        return [
            'APP_NAME' => static::APP_NAME,
            'BONUS_COEFFICIENT' => 3.0,
            'DEBUG' => true,
        ];
    }

    function getLog(): LoggerInterface
    {
        if (!static::$logger) {
            static::$logger = new Logger(static::APP_NAME);
            if (php_sapi_name() !== 'cli') {
                // Breaks phpunit tests
                static::$logger->pushHandler(new StreamHandler('php://stdout', Logger::DEBUG));
            }
        }
        return static::$logger;
    }

    function getDB(): Facade
    {
        if (!static::$db) {
            static::$db = new Facade();
            $dbHost = getenv('DB_HOST');
            $dbName = getenv('DB_DATABASE');
            $dbUser = getenv('DB_USERNAME');
            $dbPassword = getenv('DB_PASSWORD');
            $dsn = "mysql:host=$dbHost;dbname=$dbName";
            $this->getLog()->log(Logger::DEBUG, $dsn);
            $pdo = new \PDO($dsn,$dbUser,$dbPassword);
            $pdo->query('show tables');
            static::$db::setup($dsn, $dbUser, $dbPassword, !$this->getConfig()['DEBUG']);
//            static::$db::fancyDebug(true);
        }
        return static::$db;
    }
}