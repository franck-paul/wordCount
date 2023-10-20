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
    '4.0',
    [
        'requires'    => [['core', '2.28']],
        'permissions' => 'My',
        'type'        => 'plugin',
        'settings'    => [],

        'details'    => 'https://open-time.net/?q=wordCount',
        'support'    => 'https://github.com/franck-paul/wordCount',
        'repository' => 'https://raw.githubusercontent.com/franck-paul/wordCount/master/dcstore.xml',
    ]
);
