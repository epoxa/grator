<?php

use App\Method\DebugResetCommand;
use App\Service\Services;
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

}