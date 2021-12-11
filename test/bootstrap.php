<?php

error_reporting(E_ALL);

$loader = require __DIR__.'/../vendor/autoload.php';
$loader->add('Composer\Test', __DIR__);
$loader->register(true);