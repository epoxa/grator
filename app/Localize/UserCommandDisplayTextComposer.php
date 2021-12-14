<?php

namespace App\Localize;

interface UserCommandDisplayTextComposer
{
    function composeCommandCaption(string $commandName): string;
}