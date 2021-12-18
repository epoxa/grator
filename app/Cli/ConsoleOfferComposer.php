<?php

namespace App\Cli;

use App\Method\StatusQueryCommand;
use App\Method\UserCommand;
use App\Model\Offer;
use App\Model\User;

class ConsoleOfferComposer implements DisplayOffer
{

    public function __construct(
        private User $user
    )
    {
    }

    function show(): void
    {
        $translator = new CliTranslator($this->user);
        $statusQueryCommand = new StatusQueryCommand($translator);
        $offer = $statusQueryCommand->get($this->user);
        $this->displayOfferText($offer);
        $this->displayCommands($offer, $translator);

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