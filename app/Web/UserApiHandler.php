<?php

namespace App\Web;

use App\Method\DebugResetCommand;
use App\Method\InvalidStateException;
use App\Method\PrizeAcceptCommand;
use App\Method\PrizeDeclineCommand;
use App\Method\PrizeReplaceBonusesCommand;
use App\Method\StartGameCommand;
use App\Method\StatusQueryCommand;
use App\Model\User;
use App\Service\Services;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use RedBeanPHP\RedException\SQL;

class UserApiHandler implements HttpHandler
{
    private array $methodsMap;

    public function __construct(
        private User     $loggedInUser,
    )
    {
        $this->methodsMap = [
            '/start' => [$this, 'startGame'],
            '/accept' => [$this, 'acceptPrize'],
            '/replace' => [$this, 'replacePrize'],
            '/decline' => [$this, 'declinePrize'],
            '/restart' => [$this, 'debugResetGame'],
        ];
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
            $defaultResponse->getBody()->write('[]');
            return $defaultResponse->withStatus(404);
        }
    }

    private function processRequest(ServerRequestInterface $request): array
    {
        $method = $request->getMethod();
        $path = $request->getUri()->getPath();
        Services::getLog()->debug("$method $path");
        if ($method === 'GET' && $path === '/') {
            return $this->queryStatus();
        } else if ($method === 'POST') {
            $command = $this->methodsMap[$path] ?? null;
            if ($command) return $command();
        };
        return [];
    }

    private function queryStatus(): array
    {
        $webTranslator = new WebTranslator($this->loggedInUser);
        $statusQueryCommand = new StatusQueryCommand($webTranslator);
        $offer = $statusQueryCommand->get($this->loggedInUser);
        $status = [
            'text' => $offer->getText(),
            'links' => [],
        ];
        foreach ($offer->getMethods() as $method) {
            $commandName = $method->getCommandName();
            $status['links'][] = [
                'method' => 'POST',
                'url' => "/" . $commandName,
                'caption' => $webTranslator->composeCommandCaption($commandName),
            ];
        }
        return $status;
    }

    private function startGame(): array
    {
        (new StartGameCommand())->execute($this->loggedInUser);
        return $this->queryStatus();
    }

    /**
     * @throws InvalidStateException
     */
    private function acceptPrize(): array
    {
        (new PrizeAcceptCommand())->execute($this->loggedInUser);
        return $this->queryStatus();
    }

    private function replacePrize(): array
    {
        (new PrizeReplaceBonusesCommand())->execute($this->loggedInUser);
        return $this->queryStatus();
    }

    /**
     * @throws InvalidStateException
     */
    private function declinePrize(): array
    {
        (new PrizeDeclineCommand())->execute($this->loggedInUser);
        return $this->queryStatus();
    }

    /**
     * @throws SQL
     */
    private function debugResetGame(): array
    {
        (new DebugResetCommand())->execute();
        return $this->queryStatus();
    }

    private function answerWithJson($result, ResponseInterface $defaultResponse): ResponseInterface
    {
        $json = json_encode($result);
        $defaultResponse->getBody()->write($json);
        return $defaultResponse->withHeader('Content-Type', 'application/json');
    }

}