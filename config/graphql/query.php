<?php

use GraphQL\Type\Definition\Type;

return [
    'name' => 'Query',
    'fields' => function () {
        return [
            'hello' => [
                'type' => Type::string(),
                'resolve' => function ($root, $args) {
                    return 'world';
                }
            ],
            ... include __DIR__ . '/queries/unitTypes.php'
        ];
    }
];
