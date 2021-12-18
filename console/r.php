<?php

use RedBeanPHP\R;

require_once __DIR__ . '/../vendor/autoload.php';

R::setup();
R::transaction(function () {
    R::transaction(function () {
        $bean = R::dispense('bean');
        R::store($bean);
    });
});
