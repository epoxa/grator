<?php

namespace App\Method;

use App\Service\Services;
use DateTime;
use RedBeanPHP\RedException\SQL;

class DebugResetCommand implements DebugReset
{

    /**
     * @throws SQL
     * @noinspection PhpUndefinedFieldInspection
     */
    function execute(): void // Yes extreme BIG method :)
    {
        $db = Services::getDB();

        $this->dropConstraint('user', 'c_fk_user_current_game_id');
        $this->dropConstraint('game', 'c_fk_game_item_id');
        $this->dropConstraint('game', 'c_fk_game_user_id');

        $debug = Services::getConfig()['DEBUG'];
        $db::wipeAll($debug);

        // Users

        $sampleUsers = [
            ['username' => 'Adele', 'password' => '1'],
            ['username' => 'Boris', 'password' => '2'],
            ['username' => 'Vadim', 'password' => '3'],
        ];
        foreach ($sampleUsers as $userData) {
            $userData['_type'] = 'user';
            $userData['password'] = password_hash($userData['password'], PASSWORD_DEFAULT);
            $user = $db::dispense($userData);
            $db::store($user);
        }
        if ($debug) $db::exec('ALTER TABLE user ADD UNIQUE unique_username(username)');

        // Prize items

        $sampleItems = [
            ['name' => 'iPhone 11 Pro', 'count' => 1, 'hold' => 9],
            ['name' => 'Sony VAIO Laptop', 'count' => 4, 'hold' => 9],
            ['name' => 'Slotegrator brand calendar', 'count' => 10, 'hold' => 9],
        ];
        foreach ($sampleItems as $itemData) {
            $itemData['_type'] = 'item';
            $item = $db::dispense($itemData);
            $db::store($item);
            $item['hold'] = 0;
            $db::store($item);
        }
        if ($debug) $db::exec('ALTER TABLE item ADD UNIQUE unique_name(name)');

        // Games

        $game = $db::dispense('game');
        $game->created_at = new DateTime('now');
        $game->finished_at = new DateTime('now');
        $game->item = $item;
        $game->user = $user;
        $game->money = 100000;
        $game->bonus = 100000;
        $game->processed = false;
        $db::store($game);
        $user->current_game = $game;
        $db::store($user);
        $user->current_game = null;
        $db::store($user);
        $db::exec('DELETE FROM game'); // $db::trash('game');

        // Bank

        $bank = $db::dispense([
            '_type' => 'bank',
            'total' => 100000,
            'hold' => 100000,
        ]);
        $db::store($bank);
        $bank['hold'] = 0;
        $db::store($bank);

    }

    private function dropConstraint($tableName, $fkName)
    {
        $db = Services::getDB();
        $constraintExists = $db::getCell("SELECT COUNT(*) FROM information_schema.table_constraints WHERE CONSTRAINT_NAME = '$fkName'");
        if ($constraintExists) $db::exec("ALTER TABLE $tableName DROP FOREIGN KEY $fkName");
    }

}