<?php

namespace App\Model;

use App\Method\ServicesAwareMethod;
use Traversable;

interface CommandList extends Traversable
{
    function getCount(): int;
    function getCommand(int $index): ServicesAwareMethod;
}