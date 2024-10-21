<?php

use Vertuoza\Api\Graphql\Types;

return [
    'name' => 'Query',
    'fields' => function () {
        return [
            'hello' => [
                'type' => Types::string(),
                'resolve' => function ($root, $args) {
                    return 'world';
                }
            ],
            ... include __DIR__.'/object_types/unit_types.php'
        ];
    }
];
