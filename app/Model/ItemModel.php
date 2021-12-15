<?php

namespace App\Model;

use App\Service\Services;

class ItemModel implements Item
{
    private string $name;
    private int $rest;

    public function __construct(
         private int $id
    )
    {
        $bean = Services::getDB()::load('item', $this->id);
        $this->name = $bean['name'];
        $this->rest = $bean['count'];
    }

    function getId(): int
    {
        return $this->id;
    }

    function getName(): string
    {
        return $this->name;
    }

    function fundRest(): int
    {
        return $this->rest;
    }
}