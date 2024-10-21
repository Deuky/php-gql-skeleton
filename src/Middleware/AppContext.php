<?php

namespace Vertuoza\Middleware;

use Closure;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Vertuoza\Entities\UserRequestContext;
use Vertuoza\Factories\UseCasesFactory;
use Vertuoza\Kernel;

class AppContext
{
    /**
     * @var ServerRequestInterface
     */
    public ServerRequestInterface $request;

    /**
     * @var UserRequestContext
     */
    public UserRequestContext $userContext;

    /**
     * @var UseCasesFactory
     */
    public UseCasesFactory $useCases;

    /**
     * @var array
     */
    public array $headers = [];

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
     * @param ServerRequestInterface $request
     * @param callable $next
     *
     * @return mixed
     */
    public function __invoke(ServerRequestInterface $request, callable $next): mixed
    {
        $kernel = Kernel::getInstance();

        $this->useCases = new UseCasesFactory();
        $this->request = $request;
        $this->userContext = $kernel->getUserContext();

        return $next(
            $request->withAttribute('app-context', $this)
        )->then(function (ResponseInterface $response) use ($kernel) {
            foreach ($this->headers as $header) {
                foreach ($header as $name => $value) {
                    $response = $response->withHeader($name, $value);
                }
            }

            $kernel->getDatabase()->getConnection()->disconnect();
            return $response;
        });
    }
}