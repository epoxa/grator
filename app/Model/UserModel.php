<?php

namespace App\Model;

use App\Service\Services;

class UserModel implements User
{

    private string $username;
    private ?int $currentGameId = null;

    public function __construct(
        private int $id,
    )
    {
        $bean = Services::getDB()::load('user', $this->id);
        $this->username = $bean['username'];
        $this->currentGameId = $bean['current_game_id'];
    }

    function getId(): ?int
    {
        return $this->id;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    static function authorize(string $userName, string $password): ?User
    {
        $bean = Services::getDB()::findOne('user', 'username = ?', [$userName]);
        if (!$bean) return null;
        if (!password_verify($password, $bean['password'])) return null;
        return new static($bean['id']);
    }

    function getCurrentGame(): ?Game
    {
        $db = Services::getDB();
        $game = $db::findOne('game', 'id = ?', [$this->currentGameId]);
        if (!$game) return null;
        return GameRepository::loadGame($game['id']);
    }

    function setCurrentGame(?Game $game): void
    {
        $gameId = $game?->getId();
        $this->currentGameId = $gameId;
        Services::getDB()::exec("UPDATE user SET current_game_id = ? WHERE id = ?", [$gameId, $this->id]);
    }
}