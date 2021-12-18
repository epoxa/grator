<?php

namespace App\Cli;

use App\Method\DebugResetCommand;
use App\Method\InvalidStateException;
use App\Method\PrizeAcceptCommand;
use App\Method\PrizeDeclineCommand;
use App\Method\PrizeReplaceBonusesCommand;
use App\Method\StartGameCommand;
use App\Model\User;
use RedBeanPHP\RedException\SQL;

class KeyHandler implements KeyHandle
{

    private array $methodsMap;

    public function __construct(
        private User $user
    )
    {
        $this->methodsMap = [
            's' => [$this, 'startGame'],
            'a' => [$this, 'acceptPrize'],
            'c' => [$this, 'replacePrize'],
            'd' => [$this, 'declinePrize'],
            'r' => [$this, 'debugResetGame'],
            'x' => [$this, 'exitGame'],
        ];
    }

    function processKey(string $keyCode)
    {
        $method = $this->methodsMap[$keyCode] ?? null;
        if ($method) try {
            $method();
        } catch(InvalidStateException) {
            // Do nothing
        }
    }

    private function processKeyCommand($key)
    {
        $method = $this->methodsMap[$key] ?? null;
        if ($method) try {
            $method();
        } catch(InvalidStateException) {
            // Do nothing
        }
    }

    private function startGame(): void
    {
        (new StartGameCommand())->execute($this->user);
    }

    /**
     * @throws InvalidStateException
     */
    private function acceptPrize(): void
    {
        (new PrizeAcceptCommand())->execute($this->user);
    }

    /**
     * @throws InvalidStateException
     */
    private function replacePrize(): void
    {
        (new PrizeReplaceBonusesCommand())->execute($this->user);
    }

    /**
     * @throws InvalidStateException
     */
    private function declinePrize(): void
    {
        (new PrizeDeclineCommand())->execute($this->user);
    }

    /**
     * @throws SQL
     */
    private function debugResetGame(): void
    {
        Console::space();
        if (Console::askForInput("Really restart? [y/N]") !== 'y') return;
        (new DebugResetCommand())->execute();
    }

    private function exitGame(): void
    {
        $username = $this->user->getUsername();
        Console::exitConsole("Buy buy, $username...");
    }


}