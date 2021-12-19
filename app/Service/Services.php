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
    static private ?BonusProcessor $bonusProcessor = null;
    static private ?BankProcessor $bankProcessor = null;
    static private ?ItemProcessor $itemProcessor = null;

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
            'MANAGER_EMAIL' => 'dummy@mailinator.com',
            'DEBUG' => false,
        ];
    }

    /** @noinspection PhpPossiblePolymorphicInvocationInspection */
    static function getLog(): LoggerInterface
    {
        if (!static::$logger) {
            static::$logger = new Logger(static::APP_NAME);
            $osUser = exec('whoami');
            static::$logger->pushHandler(new StreamHandler(static::getConfig()['APP_ROOT'] . "/../log/log-$osUser.txt", Logger::DEBUG));
            if (php_sapi_name() !== 'cli') {
                static::$logger->pushHandler(new StreamHandler('php://stderr', Logger::INFO));
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
            static::$db::setup($dsn, $dbUser, $dbPassword, !static::getConfig()['DEBUG']);
//            static::$db::fancyDebug(true);
        }
        return static::$db;
    }

    static function getBonusProcessor(): BonusProcessor
    {
        if (!self::$bonusProcessor) {
            self::$bonusProcessor = new DummyBonusProcessor();
        }
        return self::$bonusProcessor;
    }

    static function getBankProcessor(): BankProcessor
    {
        if (!self::$bankProcessor) {
            self::$bankProcessor = new DummyBankProcessor();
        }
        return self::$bankProcessor;
    }

    static function getItemProcessor(): ItemProcessor
    {
        if (!self::$itemProcessor) {
            self::$itemProcessor = new DummyItemProcessor();
        }
        return self::$itemProcessor;
    }
}