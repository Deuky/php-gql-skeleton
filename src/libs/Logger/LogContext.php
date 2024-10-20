<?php

namespace Vertuoza\Libs\Logger;

class LogContext
{
    /**
     * @var string|null
     */
    private string|null $tenantId;

    /**
     * @var string|null
     */
    private string|null $userId;

    /**
     * @param string|null $tenantId
     * @param string|null $userId
     */
    public function __construct(string|null $tenantId, string|null $userId)
    {
        $this->tenantId = $tenantId;
        $this->userId = $userId;
    }

    /**
     * @return string|null
     */
    public function getTenantId(): string|null
    {
        return $this->tenantId;
    }

    /**
     * @return string|null
     */
    public function getUserId(): string|null
    {
        return $this->userId;
    }
}
