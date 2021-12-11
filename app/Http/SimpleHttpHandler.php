<?php

namespace App\Http;

use App\Model\User;
use App\Model\UserObject;
use App\Service\ServiceLocator;
use App\Service\Services;
use Monolog\Logger;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class SimpleHttpHandler implements HttpHandler
{

    private ServiceLocator $services;

    public function handle(ServerRequestInterface $request, ResponseInterface $defaultResponse): ResponseInterface
    {
        if ($request->getUri()->getPath() === '/logout') {
            return $this->askForCredentials($defaultResponse);
        }
        $this->services = new Services();
        $user = $this->authorize($request);
        if ($user) {
            $result = $this->processRequest($request);
            return $this->answerWithJson($result, $defaultResponse);
        } else {
            return $this->askForCredentials($defaultResponse);
        }
    }

    private function askForCredentials(ResponseInterface $defaultResponse): ResponseInterface
    {
        return $defaultResponse->withStatus(401)->withHeader('WWW-Authenticate', 'Basic realm="Access to the site"');
    }

    private function authorize(ServerRequestInterface $request): ?User
    {
        $authHeaders = $request->getHeader('Authorization');
        if (count($authHeaders) !== 1) return null;
        $auth = $authHeaders[0];
        if (!preg_match('/^Basic\s+(.+)$/',$auth,$matches)) return null;
        list($userName, $password) = explode(':', base64_decode($matches[1]));
        return (new UserObject(null, $this->services))->authorize($userName, $password);
    }

    private function processRequest(ServerRequestInterface $request): array
    {
        return [];
    }

    private function answerWithJson($result, ResponseInterface $defaultResponse): ResponseInterface
    {
        $json = json_encode($result);
        $defaultResponse->getBody()->write($json);
        return $defaultResponse->withHeader('Content-Type', 'application/json');
    }

}