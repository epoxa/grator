<?php

namespace App\Model;

use DateTime;

interface Game
{
    function getId(): int;
    function getIsFinished(): bool;
    function getCreatedTime(): DateTime;
    function getUser(): User;
    function getInitialPrize(): Prize;
    function getSelectedPrize(): Prize;
}