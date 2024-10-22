<?php

namespace Vertuoza\Exceptions;

class FieldError
{
    public string $code;
    public string $field;
    public string $path;
    public string $message;
    public array $args;

    public function __construct(string $field, string $message, string $code = "FIELD_ERROR", string $path = "", array|null $args = null)
    {
        $this->field = $field;
        $this->message = $message;
        $this->code = $code;
        $this->path = $path;
        $this->args = $args;
    }

    public function toArray()
    {
        return [
            "code" => $this->code,
            "field" => $this->field,
            "path" => $this->path,
            "message" => $this->message,
            "args" => $this->args
        ];
    }
}
