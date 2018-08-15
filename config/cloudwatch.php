<?php

return [
    'aws' => [
        'region' => env('AWS_REGION', 'us-west-2'),
        'version' => env('AWS_VERSION', 'latest'),
        'credentials' => [
            'key' => env('AWS_ACCESS_KEY_ID'),
            'secret' => env('AWS_SECRET_ACCESS_KEY'),
        ],
    ],
    'cloudwatch' => [
        'groupName' => env('AWS_CLOUDWATCH_GROUP_NAME', 'Laravel'),
        'streamName' => env('AWS_CLOUDWATCH_STREAM_NAME', 'laravel.log'),
        'streamDateFormat' => env('AWS_CLOUDWATCH_STREAM_NAME_DATE_FORMAT', null),
        'retention' => env('AWS_CLOUDWATCH_RETENTION_DAYS', 7),
    ],
];