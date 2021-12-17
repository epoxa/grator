<?php

namespace App\Cli;

/**
 * Translates user input from console to interfaces language.
 * Also translates responses produced by application methods back to console
 */

interface UI
{
    function show();
}