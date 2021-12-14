<?php

namespace App\Model;

use DateTime;

interface Game
{
    function getId(): int;
    function getStatus(): Offer;
}