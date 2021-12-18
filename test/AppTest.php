<?php /** @noinspection PhpUnhandledExceptionInspection */

use App\Method\DebugResetCommand;
use App\Method\PrizeDeclineCommand;
use App\Method\StartGameCommand;
use App\Method\StatusQueryCommand;
use App\Model\AbstractGame;
use App\Model\BonusPrizeModel;
use App\Model\Game;
use App\Model\GameRepository;
use App\Model\ItemPrizeModel;
use App\Model\MoneyPrizeModel;
use App\Model\UserModel;
use App\Service\Services;
use App\Web\WebTranslator;
use PHPUnit\Framework\TestCase;

final class AppTest extends TestCase{

    const USER_1 = 1;
    const USER_2 = 2;

    const BUTCH_SIZE = 100;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        $resetCommend = new DebugResetCommand();
        $resetCommend->execute();
    }

    private function ensureGameNotStarted(int $userID): void
    {
        $gameId = Services::getDB()::getCell('SELECT current_game_id FROM user WHERE id = ?', [$userID]);
        if ($gameId) {
            $startGameCommand = new PrizeDeclineCommand();
            $startGameCommand->execute(new UserModel($userID));
        }
    }

    private function ensureGameStarted(int $userID): void
    {
        $gameId = Services::getDB()::getCell('SELECT current_game_id FROM user WHERE id = ?', [$userID]);
        if (!$gameId) {
            $startGameCommand = new StartGameCommand();
            $startGameCommand->execute(new UserModel($userID));
        }
    }

    public function testStatus(): void
    {
        $this->ensureGameNotStarted(self::USER_1);
        $user = new UserModel(self::USER_1);
        $tr = new WebTranslator($user);
        $statusCommand = new StatusQueryCommand($tr,$tr,$tr,$tr,$tr);
        $result = $statusCommand->get($user);
        $methods = $result->getMethods();
        $this->assertCount(1, $methods);
        $this->assertEquals('start', $methods[0]->getCommandName());
        $this->assertStringContainsString('Welcome', $result->getText());
    }

    public static function callMethod($obj, $name, array $args = []) {
        $class = new \ReflectionClass($obj);
        $method = $class->getMethod($name);
        $method->setAccessible(true);
        return $method->invokeArgs(null, $args);
    }

    public function testSetupRandomBonusPrize(): void
    {
        $bonus = self::callMethod(GameRepository::class,'setupRandomBonusPrize', [Services::getConfig()]);
        $this->assertInstanceOf(BonusPrizeModel::class, $bonus);
    }

    public function testSetupRandomMoneyPrize(): void
    {
        $money = self::callMethod(GameRepository::class,'setupRandomMoneyPrize', [
            Services::getConfig()['MONEY_PRIZE']['MIN'], Services::getConfig(),  Services::getDB()
        ]);
        $this->assertInstanceOf(MoneyPrizeModel::class, $money);
        $text = $money->getOfferText(new WebTranslator(new UserModel(self::USER_1)));
        self::assertStringContainsString("<em class='money'>10.00</em>", $text);
    }

    public function testSetupRandomItemPrize(): void
    {
        $itemsFree = Services::getDB()::getAssoc('SELECT id, count - hold FROM item WHERE count > hold');
        $bonus = self::callMethod(GameRepository::class,'setupRandomItemPrize', [$itemsFree, Services::getDB()]);
        $this->assertInstanceOf(ItemPrizeModel::class, $bonus);
    }

    public function testCreateNewRandomPrizeGameInTransaction(): void
    {
        $db = Services::getDB();
        $game = $db::transaction(function () {
            $user = new UserModel(self::USER_1);
            return self::callMethod(GameRepository::class,'createNewRandomPrizeGame', [$user]);
        });
        $this->assertInstanceOf(Game::class, $game);
    }

    public function testSetupPrize(): void
    {
        $db = Services::getDB();
        $game = $db::transaction(function () {
            $game = self::callMethod(GameRepository::class, 'setupPrize', [Services::getConfig(), Services::getDB()]);
            $user = new UserModel(self::USER_1);
            $game = $game->forPlayer($user);
//            $game->store();
            return $game;
        });
        $this->assertInstanceOf(AbstractGame::class, $game);
    }

    public function testDeclineCommand(): void
    {
        $this->ensureGameStarted(self::USER_2);
        $user = new UserModel(self::USER_2);
        $declineCommand = new PrizeDeclineCommand();
        $declineCommand->execute($user);
        $currentGameId = Services::getDB()::getCell("SELECT current_game_id FROM user WHERE id = ?", [self::USER_2]);
        $this->assertNull($currentGameId);
    }



}