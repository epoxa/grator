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
        } catch (InvalidStateException $e) {
            Services::getLog()->warning($e->getMessage());
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
        $httpMethod = $request->getMethod();
        $path = $request->getUri()->getPath();
        Services::getLog()->debug("$httpMethod $path");
        if ($httpMethod === 'GET' && $path === '/') {
            return $this->queryStatus();
        } else if ($httpMethod === 'POST') {
            $command = $this->methodsMap[$path] ?? null;
            if (!$command) return [];
            $command();
            return $this->queryStatus();
        };
        return []; // 404
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
        if ($message = $this->loggedInUser->popCurrentMessage()) {
            $status['message'] = $message;
        }
        return $status;
    }

    private function startGame(): void
    {
        (new StartGameCommand())->execute($this->loggedInUser);
    }

    /**
     * @throws InvalidStateException
     */
    private function acceptPrize(): void
    {
        (new PrizeAcceptCommand())->execute($this->loggedInUser);
    }

    private function replacePrize(): void
    {
        (new PrizeReplaceBonusesCommand())->execute($this->loggedInUser);
    }

    /**
     * @throws InvalidStateException
     */
    private function declinePrize(): void
    {
        (new PrizeDeclineCommand())->execute($this->loggedInUser);
    }

    /**
     * @throws SQL
     */
    private function debugResetGame(): void
    {
        (new DebugResetCommand())->execute();
    }

    private function answerWithJson($result, ResponseInterface $defaultResponse): ResponseInterface
    {
        $json = json_encode($result);
        $defaultResponse->getBody()->write($json);
        return $defaultResponse->withHeader('Content-Type', 'application/json');
    }

}