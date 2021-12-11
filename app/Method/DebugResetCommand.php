<?php

namespace App\Method;

use App\Service\ServiceLocator;
use RedBeanPHP\RedException\SQL;

class DebugResetCommand implements DebugReset
{

    /**
     * @throws SQL
     */
    function execute(ServiceLocator $services): void
    {
        $db = $services->getDB();
        $sampleUsers = [
            ['username' => 'Adel', 'password' => password_hash('1', PASSWORD_DEFAULT)],
            ['username' => 'Bob', 'password' => password_hash('2', PASSWORD_DEFAULT)],
            ['username' => 'Vadim', 'password' => password_hash('3', PASSWORD_DEFAULT)],
        ];
        $db::exec('DROP TABLE IF EXISTS user');
        $db::wipe('user');
        foreach ($sampleUsers as $userData) {
            $userData['_type'] = 'user';
            $bean = $db::dispense($userData);
            $bean->setMeta("buildcommand.unique" , array(array('username')));
            $db::store($bean);
        }
        $db::exec('ALTER TABLE user ADD UNIQUE unique_username(username)');
    }
}