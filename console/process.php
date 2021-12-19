<?php

use App\Cli\PrizeProcessor;

require_once __DIR__ . '/../vendor/autoload.php';

// Parse params

global $argv;
$param = $argc > 1 ? intval($argv[1]) : null;
$gameId = $param > 0 ? $param : null;

// Do work

$processor = new PrizeProcessor();
$exitCode = $processor->process($gameId);

// Return result

exit($exitCode);