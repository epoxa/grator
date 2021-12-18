<?php

namespace App\Service;

class DummyBonusProcessor implements BonusProcessor
{

    function topUpBonuses(int $userId, int $bonus)
    {
        // TODO: Send message to AMQP broker
        Services::getLog()->info("User $userId got $bonus bonuses");
    }
}