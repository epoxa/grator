<?php

use App\Web\HttpGate;
use App\Web\HttpHandler;
use App\Web\SimpleAuthorizationHttpHandler;
use App\Method\DebugResetCommand;
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

    /**
     * @throws SQL
     */
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        $resetCommend = new DebugResetCommand();
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
     * @covers SimpleAuthorizationHttpHandler::authorize
     */
    public function testAnon(): void
    {
        $request = new ServerRequest('GET', '/dummy');
        $defaultResponse = new Response();
        $handler = new SimpleAuthorizationHttpHandler();
        $response = $handler->handle($request,$defaultResponse);
        $this->assertEquals(401, $response->getStatusCode());
    }

    /**
     * @covers SimpleAuthorizationHttpHandler::authorize
     */
    public function testHttpLogin(): void
    {
        $request = new ServerRequest('GET', '/');
        $request = $request->withHeader('Authorization', 'Basic ' . base64_encode('Vadim:3'));
        $defaultResponse = new Response();
        $handler = new SimpleAuthorizationHttpHandler();
        $response = $handler->handle($request,$defaultResponse);
        $this->assertEquals(200, $response->getStatusCode());
    }

    /**
     * @covers SimpleAuthorizationHttpHandler::authorize
     */
    public function testHttpStart(): void
    {
        $request = new ServerRequest('POST', '/start');
        $request = $request->withHeader('Authorization', 'Basic ' . base64_encode('Vadim:3'));
        $request = $request->withHeader('Accept', 'application/json');
        $defaultResponse = new Response();
        $handler = new SimpleAuthorizationHttpHandler();
        $response = $handler->handle($request,$defaultResponse);
        $this->assertEquals(200, $response->getStatusCode());
    }

}