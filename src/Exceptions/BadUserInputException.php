<?php

namespace Vertuoza\Exceptions;

use Throwable;
use Vertuoza\Libs\Exceptions\Array;

// To use when the user input contains data that not allowed the process to continue. Due to business rules
class BadUserInputException extends BadRequestException
{
    public const code = 400;
    /**
     * @var array<FieldError>
     */
    private array $fieldsError;


    /**
     * @param Array|FieldError $fieldsError The fields that contains the error
     * @param string $inputName The name of the input class name
     * @param Throwable $previous
     * @param array|null $args
     */
    public function __construct(array|FieldError $fieldsError, string $inputName, Throwable $previous = null, array|null $args = null)
    {
        $this->fieldsError = is_array($fieldsError) ? $fieldsError : [$fieldsError];
        $message = $inputName ? "Bad user input for {$inputName}" : "Bad user input";
        parent::__construct($message, "BAD_USER_INPUT", $previous, $args);
    }

    /**
     * @return array<FieldError>
     */
    public function getFieldsError(): array
    {
        return $this->fieldsError;
    }
}
