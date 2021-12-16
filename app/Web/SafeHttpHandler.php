<?php

namespace App\Web;

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
            Services::getLog()->error($exception->getMessage());
            Services::getLog()->error($exception->getTraceAsString());
            $response = $defaultResponse->withStatus(500);
            if (Services::getConfig()['DEBUG']) {
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