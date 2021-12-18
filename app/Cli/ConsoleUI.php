<?php

namespace App\Cli;

use App\Method\DebugResetCommand;
use App\Method\InvalidStateException;
use App\Method\PrizeAcceptCommand;
use App\Method\PrizeDeclineCommand;
use App\Method\PrizeReplaceBonusesCommand;
use App\Method\StartGameCommand;
use App\Method\StatusQueryCommand;
use App\Method\UserCommand;
use App\Model\Offer;
use App\Model\User;
use App\Model\UserModel;
use RedBeanPHP\RedException\SQL;

class ConsoleUI implements UI
{
    private int $authenticatedUserId;

    function show()
    {
        $user = CliAuthenticator::authenticate();
        $this->authenticatedUserId = $user->getId();
        do  {
            Console::clean();
            $this->displayPopupMessage($user);
            (new ConsoleOfferComposer($user))->show();
            $key = Console::readChar();
            $user = new UserModel($this->authenticatedUserId); // fresh state copy
            (new KeyHandler($user))->processKey($key);
        } while (true);
    }

    private function displayPopupMessage(User $user): void
    {
        if ($message = $user->popCurrentMessage()) {
            Console::alert($message);
            Console::space();
        }
    }

}