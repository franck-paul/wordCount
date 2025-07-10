<?php

/**
 * @brief wordCount, a plugin for Dotclear 2
 *
 * @package Dotclear
 * @subpackage Plugins
 *
 * @author Franck Paul and contributors
 *
 * @copyright Franck Paul carnet.franck.paul@gmail.com
 * @copyright GPL-2.0 https://www.gnu.org/licenses/gpl-2.0.html
 */
$this->registerModule(
    'Word Count',
    'Counts characters, words and folios, reading time of entry',
    'Franck Paul',
    '6.4',
    [
        'date'     => '2025-07-10T10:20:32+0200',
        'requires' => [
            ['core', '2.34'],
            ['TemplateHelper'],
        ],
        'permissions' => 'My',
        'type'        => 'plugin',
        'settings'    => [],

        'details'    => 'https://open-time.net/?q=wordCount',
        'support'    => 'https://github.com/franck-paul/wordCount',
        'repository' => 'https://raw.githubusercontent.com/franck-paul/wordCount/main/dcstore.xml',
        'license'    => 'gpl2',
    ]
);
