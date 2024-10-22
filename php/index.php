<?php

require_once __DIR__ . '/../vendor/autoload.php';

use GraphQL\Executor\ExecutionResult;
use GraphQL\Executor\Promise\Adapter\ReactPromiseAdapter as GraphQLReactPromiseAdapter;
use GraphQL\GraphQL;
use GraphQL\Server\ServerConfig;
use Overblog\DataLoader\DataLoader;
use React\Http\Message\Response;
use Vertuoza\Factories\SingletonFactory;
use Vertuoza\Factories\UseCasesFactory;
use Vertuoza\Kernel;
use Vertuoza\Logger\ApplicationLogger;
use Vertuoza\Logger\LogContext;
use Vertuoza\Middleware\AppContext;

$kernel = Kernel::getInstance();
$kernel->init();
$kernel->load();


die();

$useCases = new UseCasesFactory($userContext, $repositories);

echo "<pre>";
print_r(
    SingletonFactory::getInstance(AppContext::class)
);
die();
$x = $serializer->denormalize(
    [
        'use_cases' => $useCases,
        'header_context' => [],
        'user_context' => $userContext
    ],
    AppContext::class
);

print_R($x);

die();

$config = new ServerConfig();
$config->setSchema($schema);

$rawInput = file_get_contents('php://input');
$input = json_decode($rawInput, true);
$source = $input['query'];
$variableValues = $input['variables'] ?? null;
$rootValue = ['prefix' => ''];

$promise = GraphQL::promiseToExecute(
    new GraphQLReactPromiseAdapter(),
    $schema,
    $source,
    $rootValue,
    $context,
    $variableValues
);
$promise->then(function (ExecutionResult $result) use ($config) {
    if (!empty($result->errors)) {
        ApplicationLogger::getInstance()->info('Log for bodyx with error', new LogContext(null, null), $result->errors);
        $result->errors = $config->getErrorsHandler()($result->errors, $config->getErrorFormatter());
    }
});
DataLoader::await($promise);
$promise->then(function (ExecutionResult $result): Response {
    $resultArr = $result->toArray();
    return new Response(200, ['Content-Type' => 'application/json'], json_encode($resultArr, JSON_THROW_ON_ERROR));
})->then(function(Response $response) {
    echo $response->getBody();
});
