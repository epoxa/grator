<?php

namespace App\Method;

use App\Model\Game;

interface SendPrize
{
    function execute(Game $game);
}