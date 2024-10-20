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

    /**
     * @param mixed ...$args
     *
     * @return static
     */
    public function addRepositories(...$args): static
    {
        return $this->add('repositories', ...$args);
    }

    /**
     * @param string $key
     * @param string<RepositoryInterface> $repositoryClassName
     *
     * @return static
     */
    public function add(string $key, string $repositoryClassName): static
    {
        [static::$registry, 'append']($this, $key, $repositoryClassName);

        return $this;
    }

    /**
     * @return void
     */
    public function clear(): void
    {
        [static::$registry, 'set']($this, 'repositories', []);
    }
}
