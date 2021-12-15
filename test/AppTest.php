<?php

use App\Method\DebugResetCommand;
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
use RedBeanPHP\RedException\SQL;

final class AppTest extends TestCase{

    /**
     * @throws SQL
     */
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        $resetCommend = new DebugResetCommand();
        $resetCommend->execute();
    }

    /**
     * @throws SQL
     */
    public function testReset(): void
    {
        $resetCommend = new DebugResetCommand();
        $resetCommend->execute();
        $db = Services::getDB();
        $this->assertEquals(3, $db::count('user'));
    }

    public function testStatus(): void
    {
        $user = new UserModel(1);
        $tr = new WebTranslator(Services::getConfig());
        $statusCommand = new StatusQueryCommand($tr,$tr,$tr,$tr,$tr);
        $result = $statusCommand->get($user);
        $methods = $result->getMethods();
        $this->assertCount(1, $methods);
        $this->assertEquals('start', $methods[0]->getCommandName());
        $this->assertStringContainsString('Welcome', $result->getText());
    }

    public function testStartRandom(): void
    {
        $initialGamesCount = Services::getDB()::getCell('SELECT COUNT(*) FROM game');
        for ($i = 0; $i < 100; $i++) {
            $user = new UserModel(1);
            $startCommand = new StartGameCommand();
            $startCommand->execute($user);
            $gamesCount = Services::getDB()::getCell('SELECT COUNT(*) FROM game');
        }
        $this->assertEquals($initialGamesCount + 100, $gamesCount);
    }

    /**
     * @throws ReflectionException
     */
    public static function callMethod($obj, $name, array $args = []) {
        $class = new \ReflectionClass($obj);
        $method = $class->getMethod($name);
        $method->setAccessible(true);
        return $method->invokeArgs(null, $args);
    }

    public function testSetupRandomBonusPrize(): void
    {
        $bonus = self::callMethod(GameRepository::class,'setupRandomBonusPrize', [Services::getConfig()]);
        $this->assertInstanceOf(BonusPrize::class, $bonus);
    }

    public function testSetupRandomMoneyPrize(): void
    {
        $money = self::callMethod(GameRepository::class,'setupRandomMoneyPrize', [
            Services::getConfig()['MONEY_PRIZE']['MIN'], Services::getConfig(),  Services::getDB()
        ]);
        $this->assertInstanceOf(MoneyPrize::class, $money);
        $text = $money->getOfferText(new WebTranslator());
        self::assertStringContainsString("<em class='money'>10.00</em>", $text);
    }

    public function testSetupRandomItemPrize(): void
    {
        $itemsFree = Services::getDB()::getAssoc('SELECT id, count - hold FROM item WHERE count > hold');
        $bonus = self::callMethod(GameRepository::class,'setupRandomItemPrize', [$itemsFree, Services::getDB()]);
        $this->assertInstanceOf(ItemPrize::class, $bonus);
    }



}