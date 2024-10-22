<?php

namespace Vertuoza\Exceptions\Validators;

use Vertuoza\Libs\Exceptions\Validators\FieldError;

abstract class Validator
{
  protected $field;
  protected $value;
  protected $path;
  protected $errors = [];

  public function __construct($field, $value, $path = "")
  {
    $this->field = $field;
    $this->value = $value;
    $this->path = $path;
  }

  /**
   * @return array<FieldError>
   */
  function validate(): array
  {
    return $this->errors;
  }
}
