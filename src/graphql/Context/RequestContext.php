<?php

namespace Vertuoza\Api\Graphql\Context;

use Closure;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Vertuoza\Factories\UseCasesFactory;
use Vertuoza\Kernel;

class RequestContext
{
    /**
     * @var ServerRequestInterface
     */
    protected ServerRequestInterface $request;

    /**
     * @var UserRequestContext
     */
    protected UserRequestContext $userContext;

    /**
     * @var UseCasesFactory
     */
    protected UseCasesFactory $useCases;

    /**
     * @var array
     */
    protected array $headers = [];

    /**
     * @param string $cookieName
     * @param string $cookieValue
     * @param int $exp
     * @param string $domain
     * @param string $path
     * @param bool $secure
     * @param bool $httpOnly
     * @param string $sameSite
     * @return void
     *
     * @todo use cookie
     * @todo maybe create class for cookie
     */
    public function addCookie(
        string $cookieName,
        string $cookieValue,
        int $exp = 0,
        string $domain = "",
        string $path = "/",
        bool $secure = false,
        bool $httpOnly = false,
        string $sameSite = "Lax"
    ): void
    {
        $expires = gmdate('D, d M Y H:i:s T', $exp); // Converting the expiration time to the correct format
        $path = "/";

        $cookieHeader = "$cookieName=$cookieValue; Expires=$expires; Path=$path; SameSite=$sameSite";
        if ($httpOnly) {
            $cookieHeader .= "; HttpOnly";
        }
        if ($secure) {
            $cookieHeader .= "; Secure";
        }
        if ($domain !== "") {
            $cookieHeader .= " Domain=$domain;";
        }

        $this->headerContext[] = ["Set-Cookie" => $cookieHeader];
    }

    /**
     * @return bool
     */
    public function isLogged(): bool
    {
        return isset($this->userContext) && $this->userContext->isLogged();
    }

    /**
     * @return Closure
     *
     * @todo move middleware
     */

    public static function middleware(): Closure
    {
        return function (ServerRequestInterface $request, callable $next) {
            // Recreate a new connection each http call/

            $userContext = new UserRequestContext(
                '448ef4f1-56e1-48be-838c-d147b5f09705',
                '112c33ae-3dbe-431b-994d-fffffe6fd49b'
            );

            $useCases = new UseCasesFactory($userContext);

            $context = new RequestContext();
            $context->setUseCases($useCases)
                ->setRequest($request)
                ->setUserContext($userContext);

            return $next(
                $request->withAttribute('app-context', $context)
            )->then(function (ResponseInterface $response) use ($context) {
                foreach ($context->headers as $header) {
                    foreach ($header as $name => $value) {
                        $response = $response->withHeader($name, $value);
                    }
                }
                $kernel = Kernel::getInstance();

                $kernel->getDatabase()->getConnection()->disconnect();
                return $response;
            });
        };
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return static
     */
    public function setRequest(ServerRequestInterface $request): static
    {
        $this->request = $request;

        return $this;
    }

    /**
     * @param UseCasesFactory $useCases
     *
     * @return static
     */
    public function setUseCases(UseCasesFactory $useCases): static
    {
        $this->useCases = $useCases;

        return $this;
    }

    /**
     * @param UserRequestContext $userContext
     *
     * @return static
     */
    public function setUserContext(UserRequestContext $userContext): static
    {
        $this->userContext = $userContext;

        return $this;
    }

    /**
     * @return UserRequestContext
     */
    public function getUserContext(): UserRequestContext
    {
        return $this->userContext;
    }

    /**
     * @return UseCasesFactory
     */
    public function getUseCases(): UseCasesFactory
    {
        return $this->useCases;
    }
}
