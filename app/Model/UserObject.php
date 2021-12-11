<?php

namespace App\Model;

use App\Service\ServiceLocator;
use App\Service\Services;
use JetBrains\PhpStorm\Pure;

class UserObject implements User
{

    private ?string $username = null;

    public function __construct(
        private ?int $id,
        private ?ServiceLocator $services = null
    )
    {
        if (!$this->services) $this->services = new Services();
    }

    function getId(): ?int
    {
        return $this->id;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    function authorize(string $userName, string $password): ?User
    {
        $bean = $this->services->getDB()::findOne('user', 'username = ?', [$userName]);
        if (!$bean) return null;
        if (!password_verify($password, $bean['password'])) return null;
        $this->id = $bean['id'];
        $this->username = $userName;
        return $this;
    }
}