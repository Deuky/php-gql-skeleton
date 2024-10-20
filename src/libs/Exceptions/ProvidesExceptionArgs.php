<?php

namespace Vertuoza\Libs\Exceptions;

interface ProvidesExceptionArgs
{
    /**
     * Data added to define the exception
     *
     * @return array<string, mixed>|null
     */
    public function getArgs(): ?array;
}
