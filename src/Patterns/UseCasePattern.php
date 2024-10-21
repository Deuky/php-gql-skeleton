<?php

namespace Vertuoza\Patterns;

use Vertuoza\Factories\UseCaseFactory;
use Vertuoza\Interface\UseCaseInterface;
use Vertuoza\Registries\RepositoryRegistry;
use Vertuoza\Traits\UseCasePatternTrait;

abstract class UseCasePattern implements UseCaseInterface
{
    /**
     * @var class-string<UseCaseFactory>
     */
    protected static string $registry = RepositoryRegistry::class;

    use UseCasePatternTrait;

    public function __construct(
        array $useCases = []
    ) {
        [static::$registry, 'addItem']($this,
            [
                'uses'
            ]
        );
    }
}
