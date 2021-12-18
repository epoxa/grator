<?php /** @noinspection PhpUnhandledExceptionInspection */

use App\Method\DebugResetCommand;
use App\Method\InvalidStateException;
use App\Method\PrizeAcceptCommand;
use App\Method\PrizeDeclineCommand;
use App\Method\StartGameCommand;
use App\Method\StatusQueryCommand;
use App\Model\BonusPrizeModel;
use App\Model\GameRepository;
use App\Model\ItemPrizeModel;
use App\Model\MoneyPrizeModel;
use App\Model\UserModel;
use App\Service\Services;
use App\Web\WebTranslator;
use PHPUnit\Framework\TestCase;

final class ResetTest extends TestCase{

    public function testReset(): void
    {
        $resetCommend = new DebugResetCommand();
        $resetCommend->execute();
        $db = Services::getDB();
        $this->assertEquals(3, $db::count('user'));
    }

}