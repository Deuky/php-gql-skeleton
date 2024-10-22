<?php

use GraphQL\Executor\Promise\Adapter\ReactPromiseAdapter as GraphQLReactPromiseAdapter;
use GraphQL\Executor\Promise\PromiseAdapter as GraphQLPromiseAdapterInterface;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Schema;
use GraphQL\Type\SchemaConfig;
use Illuminate\Database\Capsule\Manager;
use Overblog\PromiseAdapter\Adapter\ReactPromiseAdapter as OverblogReactPromiseAdapter;
use Overblog\PromiseAdapter\PromiseAdapterInterface as OverblogPromiseAdapterInterface;
use React\Http\Message\ServerRequest;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;
use Vertuoza\Factories\Factory;
use Vertuoza\Factories\RepositoryFactory;
use Vertuoza\Factories\ServerRequestFactory;
use Vertuoza\Interface\DatabaseInterface;
use Vertuoza\Middleware\AppContext;
use Vertuoza\Middleware\GraphQLMiddleware;
use Vertuoza\Middleware\Sandbox;
use Vertuoza\Repositories\UnitTypeRepository;

return [
    // name =>  [factory,                       class, ...alias]
                [Factory::class,                AppContext::class],
                [Factory::class,                Sandbox::class],
                [Factory::class,                SchemaConfig::class],
                [Factory::class,                Schema::class],
                [Factory::class,                GraphQLMiddleware::class],
                [Factory::class,                GraphQLReactPromiseAdapter::class, GraphQLPromiseAdapterInterface::class],
                [Factory::class,                Serializer::class, SerializerInterface::class],
                [ServerRequestFactory::class,   ServerRequest::class],
                [Factory::class,                Manager::class, DatabaseInterface::class],
                [Factory::class,                ObjectType::class],
                [Factory::class,                UnitTypeRepository::class],
                [Factory::class,                OverblogReactPromiseAdapter::class, OverblogPromiseAdapterInterface::class]
];
