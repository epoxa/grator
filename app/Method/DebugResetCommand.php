<?php

namespace App\Method;

use App\Service\ServiceLocator;
use DateTime;
use RedBeanPHP\RedException\SQL;

class DebugResetCommand extends ServicesAwareMethod implements DebugReset
{

    /**
     * @throws SQL
     * @noinspection PhpUndefinedFieldInspection
     */
    function execute(): void
    {
        $db = $this->services->getDB();

        $db::wipeAll(true);

        // Users

        $sampleUsers = [
            ['username' => 'Adel', 'password' => password_hash('1', PASSWORD_DEFAULT)],
            ['username' => 'Bob', 'password' => password_hash('2', PASSWORD_DEFAULT)],
            ['username' => 'Vadim', 'password' => password_hash('3', PASSWORD_DEFAULT)],
        ];
        foreach ($sampleUsers as $userData) {
            $userData['_type'] = 'user';
            $user = $db::dispense($userData);
            $db::store($user);
        }
        $db::exec('ALTER TABLE user ADD UNIQUE unique_username(username)');

        // Prize items

        $sampleItems = [
            ['name' => 'iPhone 11 Pro', 'count' => 1],
            ['name' => 'Sony VAIO Laptop', 'count' => 3],
            ['name' => 'Slotegrator brand calendar', 'count' => 10],
        ];
        foreach ($sampleItems as $itemData) {
            $itemData['_type'] = 'item';
            $item = $db::dispense($itemData);
            $db::store($item);
        }

        // Games

        $game = $db::dispense('game');
        $game->created_at = new DateTime('now');
        $game->finished_at = new DateTime('now');
        $game->item = $item;
        $game->user = $user;
        $game->initial_prize_kind = 'money';
        $game->selected_prize_kind = 'bonus';
        $game->money = 10.0;
        $game->bonus = 10.0;
        $db::store($game);
        $user->current_game = $game;
        $db::store($user);
        $user->current_game = null;
        $db::store($user);
        $db::exec('DELETE FROM game'); // $db::trash('game');
    }
}