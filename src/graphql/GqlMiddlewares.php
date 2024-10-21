<?php

namespace Vertuoza\Api\Graphql;

use Closure;
use GraphQL\Executor\ExecutionResult;
use GraphQL\Executor\Promise\PromiseAdapter;
use GraphQL\GraphQL;
use GraphQL\Server\ServerConfig;
use Overblog\DataLoader\DataLoader;
use React\Http\Message\Response;
use React\Http\Message\ServerRequest;
use Vertuoza\Errors\GqlErrorHandler;
use Vertuoza\Kernel;
use Vertuoza\Libs\Logger\ApplicationLogger;
use Vertuoza\Libs\Logger\LogContext;
use function React\Promise\resolve;

class GqlMiddlewares
{
    /**
     * @param ServerRequest $request
     *
     * @return bool
     *
     * @deprecated not need
     */
    private static function isSandboxRoute(ServerRequest $request)
    {
        return $request->getUri()->getPath() === '/'
            && $request->getMethod() === 'GET'
            && strtolower($_ENV['SANDBOX_ACTIVE']) === 'true';
    }

    /**
     * @param $request
     *
     * @return bool
     *
     * @deprecated not need
     */
    private static function isGraphQLQueryPath($request)
    {
        $method = $request->getMethod();
        if ($method !== 'POST') {
            return false;
        }
        $prefix = "/graphql";

        return strpos($request->getUri()->getPath(), $prefix) === 0;
    }

    /**
     * @return Closure
     * @deprecated change this
     */
    public static function sandbox(): Closure
    {
        return function (ServerRequest $request, callable $next) {
            if (self::isSandboxRoute($request)) {
                $sandboxhtml = file_get_contents('./assets/sandbox.html');
                $response = Response::plaintext($sandboxhtml);

                return resolve($response->withHeader('Content-Type', 'text/html'));
            }
            return $next($request);
        };
    }

    /**
     * @param PromiseAdapter $graphQLPromiseAdapter
     *
     * @return Closure
     */
    public static function schema(PromiseAdapter $graphQLPromiseAdapter)
    {
        return function (ServerRequest $request, $next) use ($graphQLPromiseAdapter) {
            if (self::isGraphQLQueryPath($request)) {
                $kernel = Kernel::getInstance();
                $schema = $kernel->getSchema();

                $config = new ServerConfig();
                $config->setSchema($schema)
                    ->setErrorsHandler(function (array $errors, ?callable $formatter) use ($request) {
                        $context = $request->getAttribute('app-context');
                        ApplicationLogger::getInstance()->info('Log for bodyx with error', new LogContext(null, null), $errors);
                        return GqlErrorHandler::handle($errors, $formatter, $context->userContext);
                    });

                $rawInput = $request->getBody()->__toString();
                $input = json_decode($rawInput, true);
                $query = $input['query'];
                $variableValues = $input['variables'] ?? null;
                $rootValue = ['prefix' => ''];

                $handler = function (ExecutionResult $result): Response {
                    $resultArr = $result->toArray();
                    return new Response(200, ['Content-Type' => 'application/json'], json_encode($resultArr, JSON_THROW_ON_ERROR));
                };

                $promise = GraphQL::promiseToExecute($graphQLPromiseAdapter, $schema, $query, $rootValue, $request->getAttribute('app-context'), $variableValues);
                $promise->then(function (ExecutionResult $result) use ($config) {
                    if (!empty($result->errors)) {
                        ApplicationLogger::getInstance()->info('Log for bodyx with error', new LogContext(null, null), $result->errors);
                        $result->errors = $config->getErrorsHandler()($result->errors, $config->getErrorFormatter());
                    }
                });

                DataLoader::await($promise);
                return $promise->then($handler);
            }
            return $next($request);
        };
    }
}
