<?php

namespace App\Service;

class DummyItemProcessor implements ItemProcessor
{
    function notifyStaff(string $userName, string $postAddress, string $itemName)
    {
        $emailAddress = Services::getConfig()['MANAGER_EMAIL'];
        $message = "Please send $itemName to $userName. Her/his post address is: $postAddress";
//        mail($emailAddress, "Need to send prize in the mail", $message);
        sleep(1);
        Services::getLog()->info($message);
    }
}