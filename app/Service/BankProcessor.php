<?php

namespace App\Service;

interface BankProcessor
{

    function processPayment(string $cardNumber, int $amount);

}