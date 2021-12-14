<?php

use App\Method\DebugResetCommand;
use App\Method\StatusQueryCommand;
use App\Model\UserModel;
use App\Service\Services;
use App\Web\WebTranslator;
use PHPUnit\Framework\TestCase;
use RedBeanPHP\RedException\SQL;

final class AppTest extends TestCase{

    /**
     * @covers DebugResetCommand::execute
     * @throws SQL
     */
    public function testReset(): void
    {
        $services = new Services();
        $resetCommend = new DebugResetCommand($services);
        $resetCommend->execute();
        $db = $services->getDB();
        $this->assertEquals(3, $db::count('user'));
    }

    /**
     * @covers StatusQueryCommand::get
     * @noinspection PhpExpressionResultUnusedInspection
     */
    public function testStatus(): void
    {
        $services = new Services();
        $user = new UserModel(1, $services);
        $tr = new WebTranslator($services->getConfig());
        $statusCommand = new StatusQueryCommand($services,$tr,$tr,$tr,$tr,$tr);
        $result = $statusCommand->get($user);
        $methods = $result->getMethods();
        $this->assertCount(1, $methods);
        $this->assertEquals('start', $methods[0]->getCommandName());
        $this->assertStringContainsString('Welcome', $result->getText());
    }

}