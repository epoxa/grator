<?php

namespace App\Model;

use App\Localize\UITranslator;
use App\Service\Services;
use DateTime;
use RedBeanPHP\OODBBean;

class GameModel implements Game
{

    private OODBBean $bean;

    public function __construct(
        ?int $id = null,
        ?User $user = null,
    )
    {
        $db = Services::getDB();
        if ($id) {
            $this->bean = $db::load('game', $id);
        } else {
            $this->bean = $db::dispense('game');
            $this->bean['created_at'] = new DateTime();
            $this->bean['user_id'] = $user->getId();
        }
    }

    function getOfferText(UITranslator $translator): string
    {
        // TODO: Implement getOfferText() method.
    }

    function getAvailableCommands(): iterable
    {
        // TODO: Implement getAvailableCommands() method.
    }
}