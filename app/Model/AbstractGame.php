<?php

namespace App\Model;

use App\Method\PrizeAcceptCommand;
use App\Method\PrizeDeclineCommand;
use App\Service\Services;
use DateTime;
use RedBeanPHP\OODBBean;
use RedBeanPHP\RedException\SQL;

abstract class AbstractGame implements Game
{
    protected OODBBean $bean;
    protected ?User $user = null;

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
        }
    }

    function getId(): int
    {
        return $this->bean['id'];
    }

    function getUser(): User
    {
        if (!$this->user) {
            $this->user = new UserModel($this->bean['user_id']);
        }
        return $this->user;
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
            [$this->bean['user_id'], $this->getId()]);
    }

    function decline(): void
    {
        $this->deactivateGame();
        Services::getDB()::exec('DELETE FROM game WHERE id = ?', [$this->getId()]);
    }

    function scheduleProcessing()
    {
        // TODO: Can be implemented via pcntl_fork
        $gameId = $this->getId();
        $processorPath = Services::getConfig()['APP_ROOT'] . "/../console/process.php";
        $command = "php $processorPath $gameId > /dev/null &";
        Services::getLog()->debug($command);
        exec($command, $output, $ret);
        if ($ret) {
            Services::getLog()->warning("Return code not null", [
                'code' => $ret,
                'output' => $output,
            ]);
        }
    }


    function markProcessed()
    {
        Services::getDB()::exec('UPDATE game SET processed_at = CURRENT_TIMESTAMP() WHERE id = ?', [$this->getId()]);
    }
}