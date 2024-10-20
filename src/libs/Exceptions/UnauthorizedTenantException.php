<?php

namespace Vertuoza\Libs\Exceptions;

use Throwable;

class UnauthorizedTenantException extends BusinessException
{
    public const code = 404;

    protected string $tenantId;

    public function __construct(string $message, string $tenantId, Throwable $previous = null, array|null $args = null)
    {
        $this->tenantId = $tenantId;
        parent::__construct($message, "UNAUTHORIZED_TENANT", UnauthorizedTenantException::code, $previous, array_merge($args ?? [], ["tenantId" => $tenantId]));
    }

    public function getTenantId(): string
    {
        return $this->tenantId;
    }
}
