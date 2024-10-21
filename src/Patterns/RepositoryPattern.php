<?php

namespace Vertuoza\Patterns;

use Vertuoza\Interface\RepositoryInterface;
use Vertuoza\Registries\RepositoryRegistry;
use Vertuoza\Traits\RepositoryPatternTrait;

abstract class RepositoryPattern implements RepositoryInterface
{
    /**
     * @var class-string<RepositoryRegistry>
     */
    protected static string $registry = RepositoryRegistry::class;

    use RepositoryPatternTrait;

    public function __construct(
        array $repositories = []
    ) {
        [static::$registry, 'addItem']($this,
            [
                'repositories' => $repositories
            ]
        );
    }
}
