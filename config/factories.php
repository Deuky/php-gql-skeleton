<?php

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Schema;
use GraphQL\Type\SchemaConfig;
use Illuminate\Database\Capsule\Manager;
use Overblog\PromiseAdapter\Adapter\ReactPromiseAdapter;
use Overblog\PromiseAdapter\PromiseAdapterInterface;
use React\Http\Message\ServerRequest;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;
use Vertuoza\Api\Graphql\Context\RequestContext;
use Vertuoza\Factories\Factory;
use Vertuoza\Factories\RepositoryFactory;
use Vertuoza\Factories\ServerRequestFactory;
use Vertuoza\Interface\DatabaseInterface;
use Vertuoza\Repositories\Settings\UnitTypes\UnitTypeRepository;

return [
    // name =>  [factory,                       class, ...alias]
                [Factory::class,                ReactPromiseAdapter::class],
                [Factory::class,                RequestContext::class],
                [Factory::class,                SchemaConfig::class],
                [Factory::class,                Schema::class],
                [Factory::class,                Serializer::class, SerializerInterface::class],
                [ServerRequestFactory::class,   ServerRequest::class],
                [Factory::class,                Manager::class, DatabaseInterface::class],
                [Factory::class,                ObjectType::class],
                [RepositoryFactory::class,      UnitTypeRepository::class],
                [Factory::class,                ReactPromiseAdapter::class, PromiseAdapterInterface::class]
];
