<?php

namespace App\Method;

use App\Model\Game;

interface SendPrize
{
    function send(Game $game);
}