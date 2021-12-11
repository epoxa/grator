<?php

use App\Http\HttpGate;
use App\Http\SafeHttpHandler;
use App\Http\SimpleHttpHandler;

require_once __DIR__ . '/../vendor/autoload.php';

HttpGate::Process(new SafeHttpHandler(new SimpleHttpHandler()));