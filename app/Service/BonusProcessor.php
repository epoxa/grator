<?php

namespace App\Service;

interface BonusProcessor
{
    function topUpBonuses(int $userId, int $bonus);
}