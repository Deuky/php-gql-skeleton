<?php

namespace Vertuoza\Errors;

use GraphQL\Error\Error;
use Vertuoza\Entities\UserRequestContext;
use Vertuoza\Libs\Exceptions\BadUserInputException;
use Vertuoza\Libs\Exceptions\BusinessException;
use Vertuoza\Libs\Exceptions\ProvidesExceptionArgs;
use Vertuoza\Libs\Exceptions\UnauthorizedTenantException;
use Vertuoza\Libs\Logger\ApplicationLogger;
use Vertuoza\Libs\Logger\LogContext;

class GqlErrorHandler
{
    public static function handle(array $errors, ?callable $formatter, ?UserRequestContext $userContext)
    {
        $mapped = array_map(function ($error) use ($userContext) {
            $locatedError = null;
            $statusCode = 500;
            $errorCode = "UNKNOWN_ERROR";

            if ($error instanceof Error) {
                $locatedError = $error;
                if (!$error->isClientSafe()) {
                    ApplicationLogger::getInstance()->error($error, $errorCode, null, [], $statusCode);
                }
            }
            $previous = $error->getPrevious();
            if ($previous != null && $previous instanceof BusinessException) {
                $errorCode = $previous->getErrorCode();
                $statusCode = $previous->getCode();
                $message = $previous->getMessage();


                $args = [];
                if ($previous instanceof ProvidesExceptionArgs) {
                    $args = $previous->getArgs() ?? [];
                }

                $logContext = new LogContext($userContext?->getTenantId(), $userContext?->getUserId());
                ApplicationLogger::getInstance()->error($message, $errorCode, $logContext, $args, $statusCode);

                if ($previous instanceof UnauthorizedTenantException) {
                    $errorCode = "NOT_FOUND";
                    $message = "The resource is not found";
                }

                $fields = null;
                if ($previous instanceof BadUserInputException) {
                    $fieldErrors = array_merge($args, $previous->getFieldsError());
                    $fields = array_map(function ($fieldError) {
                        return $fieldError->toArray();
                    }, $fieldErrors);
                }

                if ($locatedError !== null) {
                    return new GqlClientError(
                        $message,
                        $errorCode,
                        $error->getNodes(),
                        $error->getSource(),
                        $error->getPositions(),
                        $error->getPath(),
                        $error->getPrevious(),
                        array_merge($args, ["type" => "BUSINESS_EXCEPTION", "statusCode" => $statusCode]),
                        isset($fields) ? $fields : null
                    );
                }
            }
            return $error;
        }, $errors);
        return array_map($formatter, $mapped);
    }
}
