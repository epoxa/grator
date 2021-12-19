<?php

namespace App\Model;

use App\Localize\UITranslator;
use App\Service\Services;
use DateTime;
use Error;
use RedBeanPHP\Facade;
use RedBeanPHP\OODBBean;

class GameRepository implements GameCreator, GameLoader, GameFinder
{

    static function createNewRandomPrizeGame(User $player): Game
    {
        $db = Services::getDB();
        return $db::transaction(function () use($db, $player) {
            $newGame = static::setupPrize(Services::getConfig(), $db)->forPlayer($player);
            $newGame->store();
            return $newGame;
        });
    }

    static private function setupPrize(array $config, Facade $db): AbstractGame
    {
        // Bonus prize is always possible

        $prizeKindRoulette = [
            fn() => static::setupRandomBonusPrize($config)
        ];

        // Money prize if we have enough money

        $freeMoney = $db::getCell('SELECT total - hold FROM bank');
        if ($freeMoney > $config['MONEY_PRIZE']['MIN']) {
            $prizeKindRoulette[] = fn() => static::setupRandomMoneyPrize($freeMoney, $config, $db);
        }

        // Item prize if still exists

        $itemsFree = $db::getAssoc('SELECT id, count - hold FROM item WHERE count > hold');
        if ($itemsFree) {
            $prizeKindRoulette[] = fn() => static::setupRandomItemPrize($itemsFree, $db);
        }

        // Actually select prize

        return static::runRoulette($prizeKindRoulette);
    }

    static private function runRoulette(array $roulette): AbstractGame
    {
        $idx = rand(0, count($roulette) - 1);
        return $roulette[$idx](); // Call associated setup method closure
    }

    static private function setupRandomBonusPrize(array $config): AbstractGame
    {
        $bonusPrizeAmount = rand($config['BONUS_PRIZE']['MIN'], $config['BONUS_PRIZE']['MAX']);
        Services::getLog()->info("Bonus: " . $bonusPrizeAmount);
        return new BonusPrizeModel($bonusPrizeAmount);
    }

    static private function setupRandomMoneyPrize(int $freeMoney, array $config, Facade $db): AbstractGame
    {
        $maxMoney = min($freeMoney, $config['MONEY_PRIZE']['MAX']);
        $moneyPrizeAmount = rand($config['MONEY_PRIZE']['MIN'], $maxMoney);
        Services::getLog()->info("Money: " . $moneyPrizeAmount);
        $db::exec('UPDATE bank SET hold = hold + ?', [$moneyPrizeAmount]);
        return new MoneyPrizeModel($moneyPrizeAmount);
    }

    /** @noinspection PhpInconsistentReturnPointsInspection */
    static private function setupRandomItemPrize(array $availableItems, Facade $db): AbstractGame
    {
        $total = array_sum($availableItems);
        $need = rand(1, $total);
        $accum = 0;
        foreach ($availableItems as $itemId => $rest) {
            $accum += $rest;
            if ($accum >= $need) {
                $itemModel = new ItemModel($itemId);
                Services::getLog()->info("Item: " . $itemModel->getName());
                $db::exec('UPDATE item SET hold = hold + 1 WHERE id = ?', [$itemId]);
                return new ItemPrizeModel($itemModel);
            }
        }
    }

    static function loadGame(int $gameId): Game
    {
        $db = Services::getDB();
        $bean = $db::load('game', $gameId);
        if ($bean['bonus']) {
            return new BonusPrizeModel(null, $gameId);
        } else if ($bean['money']) {
            return new MoneyPrizeModel(null, $gameId);
        } else if ($bean['item_id']) {
            return new ItemPrizeModel(null, $gameId);
        } else {
            return new DeclinedPrize($gameId);
        }
    }

    static function findUnprocessedGames(int $maxCountLimit): Iterable
    {
        $db = Services::getDB();
        $games = [];
        $foundBeans = $db->find('game', "finished_at IS NOT NULL AND processed_at IS NULL ORDER BY finished_at LIMIT $maxCountLimit");
        foreach ($foundBeans as $bean) {
            $games[] = static::loadGame($bean['id']);
        }
        return $games;
    }
}