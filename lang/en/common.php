<?php

return [
    'title' => 'Githelper',

    'messages' => [
        'tagpush' => [
            'success' => 'Repository was successfully tagged with :tag and pushed to remote',
        ],
        'readme'  => [
            'success' => 'Readme file was succesfully created',
            'exists'  => 'Readme file already exists in given location',
        ],
        'untag' => [
            'success' => 'Last tag :tag was succesfully removed from repository',
        ],
        'refresh' => [
            'success' => 'Repository information was succesfully refreshed',
        ],
        'align' => [
            'noversion' => 'Version was not specified',
            'success' => 'Repositories were succesfully set to version :align with message you :message'
        ]
    ],

    'buttons' => [
        'readme'  => 'Create Readme file',
        'tagpush' => [
            'patch' => 'Increase tag version and push',
            'minor' => 'Increase tag minor version and push',
            'major' => 'Increase tag major version and push',
        ],
        'refresh' => 'Flush cache',
        'untag' => 'Remove last used tag',
    ],
];