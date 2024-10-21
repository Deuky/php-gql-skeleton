<?php

require_once __DIR__ . '/../vendor/autoload.php';

use GraphQL\Executor\Promise\Adapter\ReactPromiseAdapter as GraphQLReactPromiseAdapter;
use React\Http\Message\Response;
use React\Http\Message\ServerRequest;
use React\Http\Middleware\RequestBodyBufferMiddleware;
use React\Http\Middleware\RequestBodyParserMiddleware;
use React\Http\Middleware\StreamingRequestMiddleware;
use Vertuoza\Kernel;
use Vertuoza\Libs\Logger\ApplicationLogger;
use Vertuoza\Libs\Logger\LogContext;
use Vertuoza\Libs\Logger\Logger;
use function React\Promise\resolve;

$kernel = Kernel::getInstance();
$kernel->init();
$kernel->load();

$logger = new Logger(
    ApplicationLogger::getInstance()
);

ini_set('memory_limit', $_ENV['MEMORY_LIMIT']);

try {
    $graphQLPromiseAdapter = new GraphQLReactPromiseAdapter();

    $http = new React\Http\HttpServer(
        new StreamingRequestMiddleware(),
        new RequestBodyBufferMiddleware(20 * 1024 * 1024),
        new RequestBodyParserMiddleware(20 * 1024 * 1024, 1),
        $kernel->getSandbox(),
        $kernel->getAppContext(),
        $kernel->getGraphQLMiddleware(),
        fn (ServerRequest $request) => resolve(new Response(404))
    );

    $socket = new React\Socket\SocketServer('0.0.0.0:' . $_ENV['PORT']);
    $http->listen($socket);

    ApplicationLogger::getInstance()->info("Server running on port " . $_ENV['PORT'] . PHP_EOL, new LogContext(null, null));
} catch (Throwable $e) {
    ApplicationLogger::getInstance()->error($e, 'UNKNOWN_ERROR', new LogContext(null, null));
    exit(0);
}
