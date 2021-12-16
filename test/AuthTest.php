<?php /** @noinspection PhpUnhandledExceptionInspection */

use App\Model\UserModel;
use PHPUnit\Framework\TestCase;

final class AuthTest extends TestCase {

    public function testAuth(): void
    {
        $user = UserModel::authorize('Vadim', '3');
        $this->assertNotNull($user);
        $this->assertEquals('Vadim', $user->getUsername());
    }

}