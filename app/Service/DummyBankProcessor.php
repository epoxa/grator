<?php

namespace App\Service;

class DummyBankProcessor implements BankProcessor
{

    function processPayment(string $cardNumber, int $amount)
    {
        // TODO Make an HTTPS request to processing center
        sleep(5);
        $formatted = number_format($amount / 100, 2, '.', '');
        Services::getLog()->info("$formatted $ transferred to card $cardNumber");
    }
}