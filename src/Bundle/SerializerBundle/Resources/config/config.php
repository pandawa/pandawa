<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Default Serializer
    |--------------------------------------------------------------------------
    |
    | Default serializer will be aliased with Symfony\Component\Serializer\SerializerInterface
    |
    */
    'default' => 'default',

    /*
    |--------------------------------------------------------------------------
    | Define Serializers
    |--------------------------------------------------------------------------
    |
    | Here you can define your serializers.
    |
    */
    'serializers' => [
        'default' => [
            'normalizers' => [
                '@serializer.normalizer.enum',
                '@serializer.normalizer.date_time',
                '@serializer.normalizer.object',
            ],
            'encoders' => [
                '@serializer.encoder.json',
                '@serializer.encoder.xml',
                '@serializer.encoder.csv',
                '@serializer.encoder.yaml',
            ],
        ]
    ]
];
