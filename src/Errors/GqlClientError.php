<?php

namespace Vertuoza\Errors;

use GraphQL\Error\ClientAware;
use GraphQL\Error\Error;
use GraphQL\Error\ProvidesExtensions;
use GraphQL\Language\Source;
use Throwable;

class GqlClientError extends Error implements ClientAware, ProvidesExtensions
{
  public function __construct(
    string $message,
    string $errorCode,
    $nodes = null,
    ?Source $source = null,
    ?array $positions = null,
    ?array $path = null,
    ?Throwable $previous = null,
    array $args = null,
    array $fields = null
  ) {
    // make sure everything is assigned properly
    parent::__construct($message, $nodes, $source, $positions, $path, $previous, [
      "code" => $errorCode,
      "args" => $args,
      "fields" => $fields
    ]);
  }

  public function isClientSafe(): bool
  {
    return true;
  }
}
