<?php

namespace App\Method;

use ErrorException;

class InvalidStateException extends ErrorException
{
    const GAME_NOT_STARTED = 'Game not started';
}