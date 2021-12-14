<?php

namespace App\Web;

use App\Method\InvalidStateException;
use App\Method\StartGameCommand;
use App\Method\StatusQueryCommand;
use App\Model\User;
use App\Service\Services;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class UserAuthorizedHttpHandler implements HttpHandler
{
    public function __construct(
        private User     $loggedInUser,
        private Services $services,
    )
    {
    }

    function handle(ServerRequestInterface $request, ResponseInterface $defaultResponse): ResponseInterface
    {
        try {
            $result = $this->processRequest($request);
        } catch (InvalidStateException) {
            return $defaultResponse->withStatus(400, 'Inappropriate command');
        }
        if ($result) {
            return $this->answerWithJson($result, $defaultResponse);
        } else {
            return $defaultResponse->withStatus(404);
        }
    }

    private function processRequest(ServerRequestInterface $request): array
    {
        $method = $request->getMethod();
        $path = $request->getUri()->getPath();
        if ($method === 'GET' && $path === '/') {
            return $this->queryStatus();
        } else if ($method === 'POST') {
            if ($path === '/start') {
                return $this->startGame();
            }
        };
        return [];
    }

    private function queryStatus(): array
    {
        $tr = new WebTranslator($this->services->getConfig());
        $statusQueryCommand = new StatusQueryCommand($this->services, $tr, $tr, $tr, $tr, $tr);
        $offer = $statusQueryCommand->get($this->loggedInUser);
        $status = [
            'text' => $offer->getText(),
            'links' => [],
        ];
        foreach ($offer->getMethods() as $method) {
            $commandName = $method->getCommandName();
            $status['links'][$commandName] = [
                'method' => 'POST',
                'url' => "/" . $commandName,
                'caption' => $tr->composeCommandCaption($commandName),
            ];
        }
        return $status;
    }

    private function startGame(): array
    {
        (new StartGameCommand($this->services))->execute($this->loggedInUser);
        return $this->queryStatus();
    }

    private function answerWithJson($result, ResponseInterface $defaultResponse): ResponseInterface
    {
        $json = json_encode($result);
        $defaultResponse->getBody()->write($json);
        return $defaultResponse->withHeader('Content-Type', 'application/json');
    }

}