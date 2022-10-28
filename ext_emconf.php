<?php

/**
 * @var array $EM_CONF
 * @var string $_EXTKEY
 */

$EM_CONF[$_EXTKEY] = [
    'title' => 'in2publish Core',
    'description' => 'Content publishing extension to connect stage and production server',
    'category' => 'plugin',
    'version' => '10.2.6',
    'state' => 'stable',
    'uploadfolder' => 0,
    'createDirs' => '',
    'clearcacheonload' => 1,
    'author' => 'Alex Kellner, Oliver Eglseder, Thomas Scheibitz, Stefan Busemann',
    'author_email' => 'service@in2code.de',
    'author_company' => 'in2code.de',
    'constraints' => [
        'depends' => [
            'php' => '7.2.0-7.4.99',
            'typo3' => '10.4.0-10.99.99',
        ],
        'conflicts' => [],
        'suggests' => [],
    ],
];
