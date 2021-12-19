<?php /** @noinspection PhpUnhandledExceptionInspection */

use App\Cli\PrizeProcessor;
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

final class ProcessTest extends TestCase{

    const ITEM_ID = 3;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        $resetCommend = new DebugResetCommand();
        $resetCommend->execute();
    }

    public static function callMethod($obj, $name, array $args = []) {
        $class = new \ReflectionClass($obj);
        $method = $class->getMethod($name);
        $method->setAccessible(true);
        return $method->invokeArgs(null, $args);
    }

    function testMoneyProcess() {
        $moneyGame = self::callMethod(GameRepository::class,'setupRandomMoneyPrize', [
            Services::getConfig()['MONEY_PRIZE']['MAX'], Services::getConfig(),  Services::getDB()
        ]);
        /** @var MoneyPrizeModel $moneyGame */
        $this->assertInstanceOf(MoneyPrizeModel::class, $moneyGame);
        $user = new UserModel(1);
        $moneyGame->forPlayer($user)->store();
        $initialMoneyRest = Services::getDB()::getCell('SELECT total FROM bank');
        $moneyGame->accept(new WebTranslator($user));
        $gameId = $moneyGame->getId();
        $newMoneyRest = Services::getDB()::getCell('SELECT total FROM bank');
        $this->assertEquals($initialMoneyRest - $moneyGame->getMoney(), $newMoneyRest);
        $processedTime = Services::getDB()::getCell('SELECT processed_at FROM game WHERE id = ?', [$gameId]);
        $this->assertNull($processedTime);
        $moneyGame->scheduleProcessing();
        sleep(1);
        $processedTime = Services::getDB()::getCell('SELECT processed_at FROM game WHERE id = ?', [$gameId]);
        $this->assertNull($processedTime);
        sleep(3);
        $processedTime = Services::getDB()::getCell('SELECT processed_at FROM game WHERE id = ?', [$gameId]);
        $this->assertNotNull($processedTime);
    }

    function testItemProcess() {
        $freeItem = Services::getDB()::getAssoc('SELECT id, count - hold FROM item WHERE id = ?', [self::ITEM_ID]);
        $initialItemCount = Services::getDB()::getCell('SELECT count FROM item WHERE id = ?', [self::ITEM_ID]);
        $itemGame = self::callMethod(GameRepository::class,'setupRandomItemPrize', [
            $freeItem, Services::getDB()
        ]);
        /** @var ItemPrizeModel $itemGame */
        $this->assertInstanceOf(ItemPrizeModel::class, $itemGame);
        $user = new UserModel(2);
        $itemGame->forPlayer($user)->store();
        $itemId = $itemGame->getItem()->getId();
        $this->assertEquals(self::ITEM_ID, $itemId);
        $itemCount = Services::getDB()::getCell('SELECT count FROM item WHERE id = ?', [$itemId]);
        $this->assertEquals($initialItemCount, $itemCount);
        $itemGame->accept(new WebTranslator($user));
        $itemCount = Services::getDB()::getCell('SELECT count FROM item WHERE id = ?', [$itemId]);
        $this->assertEquals($initialItemCount - 1, $itemCount);
        $gameId = $itemGame->getId();
        $processedTime = Services::getDB()::getCell('SELECT processed_at FROM game WHERE id = ?', [$gameId]);
        $this->assertNull($processedTime);
        $this->expectOutputRegex('/^Game processed: \d+$/');
        (new PrizeProcessor)->process($gameId);
        $processedTime = Services::getDB()::getCell('SELECT processed_at FROM game WHERE id = ?', [$gameId]);
        $this->assertNotNull($processedTime);
    }

}