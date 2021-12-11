<?php

namespace App\Model;

interface User
{
    function getId(): ?int;
    function getUsername(): ?string;
    function authorize(string $userName, string $password): ?self;
}