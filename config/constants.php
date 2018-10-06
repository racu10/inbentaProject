<?php

/*
 * Constant variables 
 */

return [
    'files' => [
        // All constant files location as string
        'allConversations' => storage_path('app\\contactInfoHist.json'),
    ],
    'botMessages' => [
        'greet' => 'Hello! I will ask you some questions ok?',
        'byebye' => 'Thanks! Now I know you better. ',
        'attributes' => [
            // The atributes defines all chat questions.
            // The order are asc depending de value of the key order.
            // In botMessages in case that needs some validation or error must appear there.
            // attribute, order and ask -> must been correctly declared
            [
                ['attribute' => 'name',
                    'order' => 10,
                    'ask' => 'What is your name?',
                ],
                [
                    'attribute' => 'email',
                    'order' => 20,
                    'ask' => 'What is your email?',
                    'validation' => 'Email must be a valid address',
                    'error' => 'Sorry, I could not understand your email address',
                ],
                [
                    'attribute' => 'age',
                    'order' => 30,
                    'age' => 'Sorry, I could not understand your age',
                    'validation' => 'Age must be a positive number',
                    'ask' => 'What is your age?',
                    'error' => 'Sorry, I could not understand your age',
                ],
            ]
        ],
    ],
    'ranges' => [
        // Set ranges for variables
        'age' => [
            'min' => 10,
            'max' => 120,
        ]
    ],
    'statusChatbot' => [
        'NO_CHANGES_NOTHING' => -1,
        'OK' => 0,
        'ERROR' => 1,
        'END' => 99,
    ]
];
