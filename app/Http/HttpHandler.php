<?php

namespace App\Http;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

interface HttpHandler
{
    function handle(ServerRequestInterface $request, ResponseInterface $defaultResponse): ResponseInterface;
}