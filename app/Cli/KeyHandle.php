<?php

namespace App\Cli;

interface KeyHandle
{
    function processKey(string $keyCode);
}