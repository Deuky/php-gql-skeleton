<?php

namespace Vertuoza;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Schema;
use GraphQL\Type\SchemaConfig;
use Illuminate\Database\Capsule\Manager;
use Overblog\PromiseAdapter\PromiseAdapterInterface;
use React\Http\Message\ServerRequest;
use Symfony\Component\Serializer\Serializer;
use Vertuoza\Api\Graphql\Resolvers\Query;
use Vertuoza\Api\Graphql\Types;
use Vertuoza\Entities\UserRequestContext;
use Vertuoza\Factories\SingletonFactory;
use Vertuoza\Interface\DatabaseInterface;
use Vertuoza\Patterns\SingletonPattern;

class Kernel extends SingletonPattern
{
    /**
     * @var array
     */
    protected array $containers;

    /**
     * @var Manager
     */
    protected Manager $database;

    /**
     * @var ServerRequest
     */
    protected ServerRequest $serverRequest;

    /**
     * @var PromiseAdapterInterface
     */
    protected PromiseAdapterInterface $promiseAdapter;

    /**
     * @var ObjectType
     */
    protected ObjectType $query;

    /**
     * @var Schema
     */
    protected Schema $schema;

    /**
     * @var SchemaConfig
     */
    protected SchemaConfig $schemaConfig;
    protected array $repositories;
    protected array $useCases;
    protected UserRequestContext $userRequestContext;

    public function init(): void
    {
        $this->containers = [
            'factory' => []
        ];
    }

    /**
     * @return void
     */
    public function load(): void
    {
        $factoryMapper = $this->getConfigContent('factories');

        foreach ($factoryMapper as $name => $factorySetup)
        {
            $factory = array_shift($factorySetup);
            $className = array_shift($factorySetup);

            $index = $className;

            if (!is_numeric($name)) {
                $index = $name;
            }

            $this->containers['factory'][$index] = new $factory($className);

            foreach ($factorySetup as $alias) {
                $this->containers['factory'][$alias] = &$this->containers['factory'][$index];
            }
        }
    }

    public function getServerRequest(): ServerRequest
    {
        return $this->serverRequest ??= $this->containers['factory'][ServerRequest::class]
            ->setHeaders()
            ->setServer()
            ->newInstance();
    }

    /**
     * @return Manager
     */
    public function getDatabase(): Manager
    {
        return $this->database ??= $this->containers['factory'][DatabaseInterface::class]
            ->addSetter('connection', $this->getConfigContent('databases'))
            ->newInstance();
    }

    public function getQuery(): ObjectType
    {
        return $this->query ??= $this->containers['factory'][ObjectType::class]
            ->addConstructorArgument('config', $this->getConfigContent('object_type'))
            ->newInstance();
    }

    /**
     * @return Schema
     */
    public function getSchema(): Schema
    {
        return $this->schema ??= $this->containers['factory'][Schema::class]
            ->addConstructorArgument('config', $this->getSchemaConfig())
            ->newInstance();
    }

    /**
     * @return SchemaConfig
     */
    public function getSchemaConfig(): SchemaConfig
    {
        return $this->schemaConfig ??= $this->containers['factory'][SchemaConfig::class]
            ->addSetter('query', $this->getQuery())
            ->addSetter('typeLoader', [Types::class, 'byTypeName'])
            ->newInstance();
    }

    /**
     * @return Serializer
     */
    public function serializer(): Serializer
    {
        die('lklk');
        return SingletonFactory::getInstance(Serializer::class);
    }

    /**
     * @return string
     */
    public function getConfigFolder(): string
    {
        return __DIR__.'/../config';
    }

    /**
     * @param string $filename
     *
     * @return string
     */
    public function getConfigFilename(string $filename): string
    {
        return $this->getConfigFolder().'/'.$filename.'.php';
    }

    /**
     * @param string $filename
     *
     * @return mixed
     */
    public function getConfigContent(string $filename): mixed
    {
        return include $this->getConfigFilename($filename);
    }

    public function getPromiseAdapter()
    {
        return $this->promiseAdapter ??= $this->containers['factory'][PromiseAdapterInterface::class]
            ->newInstance();
    }

    public function getRepository(string $repositoryClassName)
    {
        return $this->repositories[$repositoryClassName] ??= $this->containers['factory'][$repositoryClassName]
            ->addConstructorArgument('database', $this->getDatabase())
            ->addConstructorArgument('promiseAdapter', $this->getPromiseAdapter())
            ->newInstance();
    }

    public function getUseCase(string $useCaseClassName)
    {
        return $this->useCases[$useCaseClassName] ??= $this->containers['factory'][$useCaseClassName]
            ->addConstructorArgument('database', $this->getDatabase())
            ->addConstructorArgument('promiseAdapter', $this->getPromiseAdapter())
            ->newInstance();
    }

    /**
     * @return UserRequestContext
     */
    public function getUserContext(): UserRequestContext
    {
        return $this->userRequestContext ??= new UserRequestContext(
            '448ef4f1-56e1-48be-838c-d147b5f09705',
            '112c33ae-3dbe-431b-994d-fffffe6fd49b'
        );
    }
}
