<?php

namespace App\Model;

interface User
{
    static function authorize(string $userName, string $password): ?self;
    function getId(): ?int;
    function getUsername(): ?string;
    function getCurrentGame(): ?Game;
    function setCurrentGame(?Game $game): void;
}