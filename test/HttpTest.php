<?php

use App\Http\HttpGate;
use App\Http\HttpHandler;
use App\Http\SimpleHttpHandler;
use App\Method\DebugResetCommand;
use App\Service\ServiceLocator;
use App\Service\Services;
use Nyholm\Psr7\Response;
use Nyholm\Psr7\ServerRequest;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use RedBeanPHP\RedException\SQL;

final class MockHttpHandler implements HttpHandler {

    function handle(ServerRequestInterface $request, ResponseInterface $defaultResponse): ResponseInterface
    {
        $defaultResponse->getBody()->write(json_encode($request->getQueryParams()));
        return $defaultResponse;
    }

}

final class HttpTest extends TestCase{

    static ServiceLocator $services;

    /**
     * @throws SQL
     */
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::$services = new Services();
        $resetCommend = new DebugResetCommand(self::$services);
        $resetCommend->execute();
    }


    /**
     * @covers
     * @runInSeparateProcess // To avoid "output already started" issue
     */
    public function testGate(): void
    {
        define('PARAM_VALUE', 123);
        $_GET['test_param'] = PARAM_VALUE;
        ob_start();
        HttpGate::Process(new MockHttpHandler());
        $output = ob_get_clean();
        $params = json_decode($output, true);
        $this->assertEquals(PARAM_VALUE, $params['test_param']);
    }

    /**
     * @covers SimpleHttpHandler::authorize
     */
    public function testAnon(): void
    {
        $request = new ServerRequest('GET', '/dummy');
        $defaultResponse = new Response();
        $handler = new SimpleHttpHandler();
        $response = $handler->handle($request,$defaultResponse);
        $this->assertEquals(401, $response->getStatusCode());
    }

}