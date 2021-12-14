<?php

namespace App\Web;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Translates client requests from http language to interfaces language.
 * Also translates responses produced by application methods to json and http language back
*/

interface HttpHandler
{
    function handle(ServerRequestInterface $request, ResponseInterface $defaultResponse): ResponseInterface;
}