<?php

namespace Vertuoza\Factories;

use Vertuoza\Patterns\FactoryPattern;

class ServerRequestFactory extends FactoryPattern
{
    /**
     * @param array|null $allHeaders
     *
     * @return $this
     */
    public function setHeaders(array $allHeaders = null): static
    {
        if (!$allHeaders) {
            $allHeaders = getallheaders();
        }

        $headers = [];
        foreach ($allHeaders as $key => $value) {
            $headers[$key] = [$value];
        }

        $this->addConstructorArgument('headers', $headers);

        return $this;
    }

    /**
     * @param array|null $server
     *
     * @return static
     */
    public function setServer(array $server = null): static
    {
        if (!$server) {
            $server = $_SERVER;
        }

        $serverParams = [];
        foreach ($_SERVER as $key => $value) {
            if (
                str_starts_with($key, 'SERVER_') ||
                str_starts_with($key, 'REMOTE_') ||
                str_starts_with($key, 'REQUEST_')
            ) {
                $serverParams[$key] = $value;
            }
        }

        $url = $server['REQUEST_SCHEME'] . '://' . $server['HTTP_HOST'] . (($server['REQUEST_URI'] ?? null) ?: '/');

        $version = explode('/', $_SERVER['SERVER_PROTOCOL'])[1] ?? null;

        $this->addConstructorArgument('method', $server['REQUEST_METHOD'])
            ->addConstructorArgument('url', $url)
            ->addConstructorArgument('serverParams', $serverParams)
            ->addConstructorArgument('version', $version);

        return $this;
    }
}
