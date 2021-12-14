<?php

namespace App\Model;

use App\Localize\DisplayTextComposer;

interface Prize
{
    function getDisplayTextComposer(): DisplayTextComposer;
    function getAvailableCommands(): CommandList;
}