<?php

namespace App\Model;

interface GameFinder
{
    static function findUnprocessedGames(int $maxCountLimit): Iterable;
}