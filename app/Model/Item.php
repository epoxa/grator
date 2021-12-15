<?php

namespace App\Model;

interface Item
{
    function getId(): int;
    function getName(): string;
    function fundRest(): int;
}