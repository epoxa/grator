<?php /** @noinspection PhpUnhandledExceptionInspection */

use App\Method\DebugResetCommand;
use App\Method\InvalidStateException;
use App\Method\PrizeAcceptCommand;
use App\Method\PrizeDeclineCommand;
use App\Method\StartGameCommand;
use App\Method\StatusQueryCommand;
use App\Model\BonusPrize;
use App\Model\GameRepository;
use App\Model\ItemPrize;
use App\Model\MoneyPrize;
use App\Model\UserModel;
use App\Service\Services;
use App\Web\WebTranslator;
use PHPUnit\Framework\TestCase;

final class BatchTest extends TestCase{

    const USER_ID = 1;
    const BUTCH_SIZE = 100;

    private function ensureGameNotStarted(int $userID): void
    {
        $gameId = Services::getDB()::getCell('SELECT current_game_id FROM user WHERE id = ?', [$userID]);
        if ($gameId) {
            $startGameCommand = new PrizeDeclineCommand();
            $startGameCommand->execute(new UserModel($userID));
        }
    }

    public function testBulkRandom(): void
    {
        $this->ensureGameNotStarted(self::USER_ID);
        $user = new UserModel(self::USER_ID);
        $initialGamesCount = Services::getDB()::getCell('SELECT COUNT(*) FROM game');
        for ($i = 0; $i < self::BUTCH_SIZE; $i++) {
            $startCommand = new StartGameCommand();
            $startCommand->execute($user);
            $acceptCommand = new PrizeAcceptCommand();
            $acceptCommand->execute($user);
        }
        $gamesCount = Services::getDB()::getCell('SELECT COUNT(*) FROM game');
        $this->assertEquals($initialGamesCount + self::BUTCH_SIZE, $gamesCount);
    }

}