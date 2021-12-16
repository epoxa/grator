<?php

namespace App\Cli;

use App\Model\User;
use App\Model\UserModel;

class CliAuthenticator
{
    const MAX_LOGIN_ATTEMPTS = 3;
    const ASK_FOR_USER_NAME_MESSAGE = "Your name";
    const ASK_FOR_PASSWORD_MESSAGE = "Password";
    const INVALID_CREDENTIALS_MESSAGE = "Invalid credentials";
    const TOO_MANY_ATTEMPTS_MESSAGE = "Too many attempts";

    static function authenticate(): User
    {
        $try = 0;
        do {
            $username = Console::askForInput(static:: ASK_FOR_USER_NAME_MESSAGE);
            $password = Console::askForSecret(static::ASK_FOR_PASSWORD_MESSAGE);
            $user = UserModel::authorize($username, $password);
            if (!$user) {
                Console::write(static::INVALID_CREDENTIALS_MESSAGE);
            }
            Console::space();
        } while (!$user && ++$try < static::MAX_LOGIN_ATTEMPTS);
        if (!$user) {
            Console::exitConsole(static::TOO_MANY_ATTEMPTS_MESSAGE);
        }
        return $user;
    }


}