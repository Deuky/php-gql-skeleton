<?php

use GraphQL\Type\Definition\ListOfType;
use GraphQL\Type\Definition\NonNull;
use GraphQL\Type\Definition\Type;
use Vertuoza\Kernel;
use Vertuoza\Middleware\AppContext;

return [
    'unitTypeById' => [
        'type' => fn() => Kernel::getInstance()->getType('UnitType'),
        'args' => [
            'id' => new NonNull(Type::string()),
        ],
        'resolve' => static fn ($rootValue, $args, AppContext $context)
            => $context->useCases->unitType
                ->unitTypeById
                ->handle($args['id'])
    ],
    'unitTypes' => [
        'type' => new NonNull(new ListOfType(fn() => Kernel::getInstance()->getType('UnitType'))),
        'resolve' => static fn ($rootValue, $args, AppContext $context)
            => $context->useCases->unitType
                ->unitTypesFindMany
                ->handle()
    ]
];
