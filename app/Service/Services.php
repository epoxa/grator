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

    static function getConfig(): array
    {
        return [
            'APP_NAME' => static::APP_NAME,
            'APP_ROOT' => realpath(__DIR__ . "/.."),
            'WEB_ROOT' => realpath(__DIR__ . "/../../web"),
            'MONEY_PRIZE' => [
                'MIN' => 1000,
                'MAX' => 10000,
            ],
            'BONUS_PRIZE' => [
                'MIN' => 1000,
                'MAX' => 10000,
                'COEFFICIENT' => 300,
            ],
            'DEBUG' => true,
        ];
    }

    static function getLog(): LoggerInterface
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

    static function getDB(): Facade
    {
        if (!static::$db) {
            static::$db = new Facade();
            $dbHost = getenv('DB_HOST');
            $dbName = getenv('DB_DATABASE');
            $dbUser = getenv('DB_USERNAME');
            $dbPassword = getenv('DB_PASSWORD');
            $dsn = "mysql:host=$dbHost;dbname=$dbName";
//            static::getLog()->log(Logger::DEBUG, $dsn . " USER: $dbUser PASSWD: $dbPassword");
//            $pdo = new \PDO($dsn,$dbUser,$dbPassword);
//            $pdo->query('show tables');
            static::$db::setup($dsn, $dbUser, $dbPassword, !static::getConfig()['DEBUG']);
//            static::$db::fancyDebug(true);
        }
        return static::$db;
    }
}