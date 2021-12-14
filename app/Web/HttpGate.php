<?php

namespace App\Web;

use Laminas\HttpHandlerRunner\Emitter\SapiEmitter;
use Nyholm\Psr7\Factory\Psr17Factory;
use Nyholm\Psr7Server\ServerRequestCreator;

class HttpGate
{
    static public function Process(HttpHandler $handler): void
    {
        // Prepare request

        $psr17Factory = new Psr17Factory();
        $creator = new ServerRequestCreator(
            $psr17Factory, // ServerRequestFactory
            $psr17Factory, // UriFactory
            $psr17Factory, // UploadedFileFactory
            $psr17Factory  // StreamFactory
        );
        $request = $creator->fromGlobals();

        // Handle requested data

        $defaultResponse = $psr17Factory->createResponse();
        $response = $handler->handle($request, $defaultResponse);

        // Send response

        (new SapiEmitter())->emit($response);
    }
}