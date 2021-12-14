<?php

namespace App\Web;

use App\Model\User;
use App\Model\UserModel;
use App\Service\Services;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class SimpleAuthorizationHttpHandler implements HttpHandler
{

    public function handle(ServerRequestInterface $request, ResponseInterface $defaultResponse): ResponseInterface
    {
        if ($request->getUri()->getPath() === '/logout') {
            return $this->askForCredentials($defaultResponse);
        }
        $services = new Services();
        $user = $this->authorize($request);
        if ($user) {
            return (new UserAuthorizedHttpHandler($user, $services))
                ->handle($request, $defaultResponse);
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
        return (new UserModel())->authorize($userName, $password);
    }



}