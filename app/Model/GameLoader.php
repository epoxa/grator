<?php

namespace App\Model;

interface GameLoader
{
    static function loadGame(int $gameId): Game;
}