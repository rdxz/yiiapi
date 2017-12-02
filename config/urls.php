<?php
return [
    ['class' => 'yii\rest\UrlRule', 
        'controller' => [
            'support-versions',
            'default',
            'v1/user', 
            'v1/post',
            'v1/test',
            'v1/article',
            'v1/stark',
            ]
    ],
    // [
    //     'class' => 'yii\rest\UrlRule',
    //     'controller' => [
    //         'v1/article'
    //     ],
    //     'extraPatterns' => [
    //         'GET index' => 'index',
    //     ]
    // ],
];