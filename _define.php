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
if (!defined('DC_RC_PATH')) {
    return;
}

$this->registerModule(
    'Word Count',                                                 // Name
    'Counts characters, words and folios, reading time of entry', // Description
    'Franck Paul',                                                // Author
    '1.2',                                                        // Version
    [
        'requires'    => [['core', '2.24']],                         // Dependencies
        'permissions' => 'usage,contentadmin',                       // Permissions
        'type'        => 'plugin',                                   // Type
        'settings'    => [],

        'details'    => 'https://open-time.net/?q=wordCount',        // Details URL
        'support'    => 'https://github.com/franck-paul/wordCount',  // Support URL
        'repository' => 'https://raw.githubusercontent.com/franck-paul/wordCount/master/dcstore.xml',
    ]
);
