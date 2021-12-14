<?php

namespace App\Model;

interface Item
{
    function getName(): string;
    function fundRest(): int;
}