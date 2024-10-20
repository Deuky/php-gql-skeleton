<?php

namespace Vertuoza\Libs\Logger;

use Throwable;

class Logger
{
    /**
     * @var ApplicationLogger
     */
    protected ApplicationLogger $logger;

    /**
     * @param ApplicationLogger $logger
     */
    public function __construct(ApplicationLogger $logger)
    {
        $this->logger = $logger;

        set_error_handler([$this, 'errorHandler']);
        set_exception_handler([$this, 'exceptionHandler']);
    }

    /**
     * @param $errno
     * @param $errstr
     * @param $errfile
     * @param $errline
     *
     * @return bool
     */
    public function errorHandler($errno, $errstr, $errfile, $errline): bool
    {
        if (!(error_reporting() & $errno)) {
            // This error code is not included in error_reporting, so let it fall
            // through to the standard PHP error handler
            $this->logger->warning($errstr, null, ['errline' => $errline, 'errfile' => $errfile]);
            return false;
        }

        // $errstr may need to be escaped:
        $errstr = htmlspecialchars($errstr);

        switch ($errno) {
            case E_CORE_ERROR:
            case E_COMPILE_ERROR:
            case E_ERROR:
            case E_USER_ERROR:
                $message = "[$errno] $errstr\r\nFatal error on line $errline in file $errfile";
                $this->logger->error($message, 'INTERNAL', null, ['errline' => $errline, 'errfile' => $errfile]);
                break;

            case E_CORE_WARNING:
            case E_WARNING:
            case E_COMPILE_WARNING:
            case E_USER_WARNING:
                $message = "[$errno] $errstr\r\nWarning on line $errline in file $errfile";
                $this->logger->warning($message, null, ['errline' => $errline, 'errfile' => $errfile]);
                break;
            case E_DEPRECATED:
                $message = "[$errno] $errstr\r\nDeprecated on line $errline in file $errfile";
                $this->logger->warning($message, null, ['errline' => $errline, 'errfile' => $errfile]);
                break;

            case E_USER_NOTICE:
                $message = "[$errno] $errstr\r\nNotice error on line $errline in file $errfile";
                $this->logger->info($message, null, ['errline' => $errline, 'errfile' => $errfile]);
                break;

            default:
                $message = "[$errno] $errstr\r\nError on line $errline in file $errfile";
                $this->logger->warning($message, null, ['errline' => $errline, 'errfile' => $errfile]);
                break;
        }

        /* Don't execute PHP internal error handler */
        return true;
    }

    /**
     * @param Throwable $exception
     *
     * @return void
     */
    public function exceptionHandler(Throwable $exception): void
    {
        $this->logger->error(
            $exception, $exception->getCode(),
            null,
            ['exception' => $exception]
        );
    }

    /**
     * @return ApplicationLogger
     */
    public function getLogger(): ApplicationLogger
    {
        return $this->logger;
    }
}
