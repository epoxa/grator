<?php

namespace App\Web;

use App\Model\User;
use App\Service\Services;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Throwable;

class ApiDispatcher implements HttpHandler
{

    public function __construct(
        private User $user
    )
    {
    }

    function handle(ServerRequestInterface $request, ResponseInterface $defaultResponse): ResponseInterface
    {
        $accept = $request->getHeader('Accept');
        if (in_array('application/json', $accept)) {
            return (new UserApiHandler($this->user))->handle($request, $defaultResponse);
        } else {
            $html = file_get_contents(Services::getConfig()['WEB_ROOT'] . '/html/index.html');
            $defaultResponse->getBody()->write($html);
            return $defaultResponse;
        }
    }

}