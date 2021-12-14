<?php

use App\Web\HttpGate;
use App\Web\SafeHttpHandler;
use App\Web\SimpleAuthorizationHttpHandler;

require_once __DIR__ . '/../vendor/autoload.php';

HttpGate::Process(new SafeHttpHandler(new SimpleAuthorizationHttpHandler()));