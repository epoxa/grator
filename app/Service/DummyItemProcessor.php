<?php

namespace App\Service;

class DummyItemProcessor implements ItemProcessor
{

    function notifyStaff(string $userName, string $postAddress, string $itemName)
    {
        $emailAddress = Services::getConfig()['MANAGER_EMAIL'];
        $message = "User $userName won $itemName. His/her post address is: $postAddress";
//        mail($emailAddress, "Need to send prize in the mail", $message);
        Services::getLog()->info($message);
    }
}