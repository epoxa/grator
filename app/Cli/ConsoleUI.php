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
use RedBeanPHP\RedException\SQL;

class ConsoleUI implements UI
{
    private User $loggedInUser;
    private array $methodsMap;

    public function __construct(
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

    function show()
    {
        $this->loggedInUser = CliAuthenticator::authenticate();
        $this->showOffer();
        do  {
            $key = Console::readChar();
            $this->processKeyCommand($key);
            $this->showOffer();
        } while (true);
    }

    private function showOffer()
    {
        Console::clean();
        $this->displayMessage();
        $translator = new CliTranslator($this->loggedInUser);
        $statusQueryCommand = new StatusQueryCommand($translator);
        $offer = $statusQueryCommand->get($this->loggedInUser);
        $this->displayOfferText($offer);
        $this->displayCommands($offer, $translator);
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
        (new StartGameCommand())->execute($this->loggedInUser);
    }

    /**
     * @throws InvalidStateException
     */
    private function acceptPrize(): void
    {
        (new PrizeAcceptCommand())->execute($this->loggedInUser);
    }

    /**
     * @throws InvalidStateException
     */
    private function replacePrize(): void
    {
        (new PrizeReplaceBonusesCommand())->execute($this->loggedInUser);
    }

    /**
     * @throws InvalidStateException
     */
    private function declinePrize(): void
    {
        (new PrizeDeclineCommand())->execute($this->loggedInUser);
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
        $username = $this->loggedInUser->getUsername();
        Console::exitConsole("Buy buy, $username...");
    }

    private function displayMessage(): void
    {
        if ($message = $this->loggedInUser->popCurrentMessage()) {
            Console::alert($message);
            Console::space();
        }
    }

    private function displayOfferText(Offer $offer): void
    {
        Console::write($offer->getText());
        Console::space();
    }

    private function displayCommands(Offer $offer, CliTranslator $translator): void
    {
        $buttons = '';
        foreach ($offer->getMethods() as $command) {
            /** @var UserCommand $command */
            $commandName = $command->getCommandName();
            $caption = $translator->composeCommandCaption($commandName);
            $buttons .= " [ $caption ]";
        }
        $resetCommandCaption = $translator->composeCommandCaption('reset');
        $exitCommandCaption = $translator->composeCommandCaption('exit');
        $buttons .= " [ $resetCommandCaption ]";
        $buttons .= " [ $exitCommandCaption ]";
        $buttons .= " ";
        Console::out($buttons);
    }


}