<?php

namespace Vertuoza\Libs\Logger;

require_once __DIR__ . '/GcpLoggerFormatter.php';
require_once __DIR__ . '/LogContext.php';

use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Psr\Log\LogLevel;
use Stringable;
use Throwable;

class ApplicationLogger
{
    private static $instance = null;
    private Logger $logger;
    private string $serviceName;

    /**
     * @param string $serviceName
     */
    private function __construct(string $serviceName)
    {
        $this->serviceName = $serviceName;
        $this->logger = new Logger('kernel');
        if (isset($_ENV['LOG_ENABLED']) && $_ENV['LOG_ENABLED'] === 'true') {
            $stream = new StreamHandler('php://stdout', LogLevel::DEBUG);
            $formatter = new GcpLoggerFormatter(['serviceName', 'tenantId']);
            $formatter->setJsonPrettyPrint(isset($_ENV['MODE']) && $_ENV['MODE'] === 'DEVELOPMENT');

            $stream->setFormatter($formatter);

            $this->logger->pushHandler($stream);
        }
    }

    /**
     * @return ApplicationLogger
     */
    public static function getInstance(): ApplicationLogger
    {
        if (self::$instance == null) {
            self::$instance = new ApplicationLogger('gql-api');
        }

        return self::$instance;
    }

    protected function computeContext(LogContext|null $logContext, array $metadata = [])
    {
        $computedContext = array_merge($metadata, ["serviceName" => $this->serviceName]);

        if ($logContext === null) {
            return $computedContext;
        }

        if ($logContext->getTenantId() !== null) {
            $computedContext['tenantId'] = $logContext->getTenantId();
        }
        if ($logContext->getUserId() !== null) {
            $computedContext['userId'] = $logContext->getUserId();
        }
        return $computedContext;
    }

    public function info(Stringable|string $message, LogContext|null $logContext, array $metadata = []): void
    {
        $this->logger->info($message, $this->computeContext($logContext, $metadata));
    }

    public function debug(Stringable|string $message, LogContext|null $logContext, array $metadata = []): void
    {
        $this->logger->debug($message, $this->computeContext($logContext, $metadata));
    }

    public function warning(Stringable|string $message, LogContext|null $logContext, array $metadata = []): void
    {
        $this->logger->warning($message, $this->computeContext($logContext, $metadata));
    }

    public function error(Stringable|string $message, string $errorCode, LogContext|null $logContext, array $metadata = [], string|null $statusCode = null): void
    {
        $context = ['errorCode' => $errorCode];
        if ($statusCode !== null) {
            $context['statusCode'] = $statusCode;
        }
        $this->logger->error($message, array_merge($context, $this->computeContext($logContext, $metadata)));
    }

    public function critical(Stringable|string $message, LogContext|null $logContext, array $metadata = []): void
    {
        $this->logger->critical($message, $this->computeContext($logContext, $metadata));
    }

    public function emergency(Stringable|string $message, LogContext|null $logContext, array $metadata = []): void
    {
        $this->logger->emergency($message, $this->computeContext($logContext, $metadata));
    }
}
