<?php

namespace Vertuoza\Api\Graphql\Context;

/**
 * @todo change class name
 */
class UserRequestContext
{
    /**
     * @var string|null
     */
    private string|null $userId;

    /**
     * @var string|null
     */
    private string|null $tenantId;

    /**
     * @param string|null $userId
     * @param string|null $tenantId
     */
    public function __construct(string|null $userId, string|null $tenantId)
    {
        $this->userId = $userId;
        $this->tenantId = $tenantId;
    }

    /**
     * @return string|null
     */
    public function getUserId(): string|null
    {
        return $this->userId;
    }

    /**
     * @return string|null
     */
    public function getTenantId(): string|null
    {
        return $this->tenantId;
    }

    /**
     * @return bool
     */
    public function isLogged(): bool
    {
        return ($this->userId ?? null) && ($this->tenantId ?? null);
    }
}
