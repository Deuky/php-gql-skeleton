<?php

namespace Vertuoza\Middleware;

use React\Http\Message\Response;
use React\Http\Message\ServerRequest;
use function React\Promise\resolve;

class Sandbox
{
    /**
     * @param ServerRequest $request
     *
     * @return bool
     */
    private static function isSandboxRoute(ServerRequest $request)
    {
        return $request->getUri()->getPath() === '/'
            && $request->getMethod() === 'GET'
            && strtolower($_ENV['SANDBOX_ACTIVE']) === 'true';
    }

    /**
     * @param ServerRequest $request
     * @param callable $next
     *
     * @return mixed
     */
    public function __invoke (ServerRequest $request, callable $next): mixed
    {
        if (!self::isSandboxRoute($request)) {
            return $next($request);
        }

        $sandboxhtml = file_get_contents('./assets/sandbox.html');
        $response = Response::plaintext($sandboxhtml);

        return resolve($response->withHeader('Content-Type', 'text/html'));
    }
}
