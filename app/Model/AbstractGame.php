<?php

namespace App\Model;

use App\Localize\UITranslator;
use App\Method\PrizeAcceptCommand;
use App\Method\PrizeDeclineCommand;
use App\Service\Services;
use DateTime;
use RedBeanPHP\OODBBean;
use RedBeanPHP\RedException\SQL;

abstract class AbstractGame implements Game
{
    protected OODBBean $bean;

    public function __construct(
        ?int $id = null,
    )
    {
        $db = Services::getDB();
        if ($id) {
            $this->bean = $db::load('game', $id);
        } else {
            $this->bean = $db::dispense('game');
            $this->bean['created_at'] = new DateTime();
            $this->bean['processed'] = false;
        }
    }

    function getId(): int
    {
        return $this->bean['id'];
    }

    public function forPlayer(User $user): static
    {
        $this->bean['user_id'] = $user->getId();
        return $this;
    }

    function getAvailableCommands(): iterable
    {
        return [
            new PrizeAcceptCommand(),
            new PrizeDeclineCommand(),
        ];
    }

    /**
     * @throws SQL
     */
    function store(): void
    {
        Services::getDB()::store($this->bean);
    }

    protected function deactivateGame(): void
    {
        Services::getDB()::exec(
            'UPDATE user SET current_game_id = null WHERE id = ?;
                UPDATE game SET finished_at = CURRENT_TIMESTAMP() WHERE id = ?',
            [$this->bean['user_id'], $this->bean['id']]);
    }

    function decline(): void
    {
        Services::getDB()::exec(
            'UPDATE game SET money = null, bonus = null, item_id = null WHERE id = ?',
            [$this->bean['id']]);
        $this->deactivateGame();
    }
}