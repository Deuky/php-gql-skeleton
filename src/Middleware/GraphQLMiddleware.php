<?php

namespace Vertuoza\Middleware;

use Closure;
use Exception;
use GraphQL\Executor\ExecutionResult;
use GraphQL\GraphQL;
use GraphQL\Server\ServerConfig;
use Overblog\DataLoader\DataLoader;
use Psr\Http\Message\ServerRequestInterface;
use React\Http\Message\Response;
use Vertuoza\Errors\GqlErrorHandler;
use Vertuoza\Kernel;
use Vertuoza\Logger\ApplicationLogger;
use Vertuoza\Logger\LogContext;

class GraphQLMiddleware
{
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
     * @param ServerRequestInterface $request
     * @param callable $next
     *
     * @return Closure
     *
     * @throws Exception
     */
    public function __invoke(ServerRequestInterface $request, callable $next): mixed
    {
        // PromiseAdapter $graphQLPromiseAdapter
        if (!self::isGraphQLQueryPath($request)) {
            return $next($request);
        }

        $kernel = Kernel::getInstance();
        $schema = $kernel->getSchema();
        $graphQLPromiseAdapter = $kernel->getGraphQLPromiseAdapter();

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
}
