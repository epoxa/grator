<?php

namespace App\Model;

interface GameCreator
{
    static function createNewRandomPrizeGame(User $player): Game;
}