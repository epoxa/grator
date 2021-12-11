<?php

namespace App\Http;

use App\Service\Services;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Throwable;

class SafeHttpHandler implements HttpHandler
{

    private HttpHandler $decorate;

    public function __construct(HttpHandler $decorate)
    {
        $this->decorate = $decorate;
    }

    function handle(ServerRequestInterface $request, ResponseInterface $defaultResponse): ResponseInterface
    {
        try {
            return $this->decorate->handle($request, $defaultResponse);
        } catch (Throwable $exception) {
            $services = new Services();
            $services->getLog()->error($exception->getMessage());
            $response = $defaultResponse->withStatus(500);
            if ($services->getConfig()['DEBUG']) {
                $response->getBody()->write('<pre>');
                $response->getBody()->write($exception->getMessage());
                $response->getBody()->write($exception->getTraceAsString());
                $response->getBody()->write($exception->getTraceAsString());
                $response->getBody()->write('</pre>');
            }
            return $response;
        }
    }

}