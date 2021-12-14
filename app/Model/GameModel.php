<?php

namespace App\Model;

use App\Service\Services;
use DateTime;
use RedBeanPHP\OODBBean;

class GameModel implements Game
{

    private OODBBean $bean;

    public function __construct(
        private int $id,
        private Services $services,
    )
    {
        $this->bean = $this->services->getDB()::findOne('game', 'id = ?', [$this->id]);
    }

    function getId(): int
    {
        return $this->id;
    }

    function getStatus(): Offer
    {
        // TODO: Implement getStatus() method.
    }
}