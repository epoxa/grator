<?php

namespace App\Service;

interface ItemProcessor
{
    function notifyStaff(string $userName, string $postAddress, string $itemName);
}