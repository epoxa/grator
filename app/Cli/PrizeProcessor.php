<?php

namespace App\Cli;

use App\Method\SendPrizeCommand;
use App\Model\Game;
use App\Model\GameRepository;
use App\Service\Services;
use Throwable;

class PrizeProcessor
{
    const BATCH_SIZE = 5;

    const EXIT_CODE_OK = 0;
    const EXIT_CODE_NO_MORE = 1;
    const EXIT_CODE_UNEXPECTED_ERROR = 2;

    function process(?int $gameId): int
    {
        return $this->safeProcess($gameId);
    }

    private function safeProcess(?int $gameId): int
    {
        try {
            return $this->parseAndProcess($gameId);
        } catch (Throwable $e) {
            $message = $e->getMessage();
            Services::getLog()->error($message . "\n" . $e->getTraceAsString());
            Console::write($message);
            return self::EXIT_CODE_UNEXPECTED_ERROR;
        }
    }

    private function parseAndProcess(?int $gameId): int
    {
        if ($gameId) {

            // Process single prize

            $game = GameRepository::loadGame($gameId);
            $this->doProcessGame($game);
            Console::write("Game processed: $gameId");
            return self::EXIT_CODE_OK;

        } else {

            // Process next batch

            $games = GameRepository::findUnprocessedGames(self::BATCH_SIZE);
            if ($games) {
                $this->processMultipleGames($games);
                return self::EXIT_CODE_OK;
            } else {
                Console::write("No unprocessed games found");
                return self::EXIT_CODE_NO_MORE;
            }

        }
    }

    private function processMultipleGames(iterable $games): void
    {
        Console::out("Games processed:");;
        foreach ($games as $game) {
            $this->doProcessGame($game);
            Console::out(" " . $game->getId());
        }
        Console::write("");
    }

    private function doProcessGame(Game $game): void
    {
        $command = new SendPrizeCommand();
        $command->execute($game);
    }

}