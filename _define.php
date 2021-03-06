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

if (!defined('DC_RC_PATH')) {return;}

$this->registerModule(
    "Word Count",                                                 // Name
    "Counts characters, words and folios, reading time of entry", // Description
    "Franck Paul",                                                // Author
    '0.6.1',                                                      // Version
    [
        'requires'    => [['core', '2.16']],                         // Dependencies
        'permissions' => 'usage,contentadmin',                       // Permissions
        'type'        => 'plugin',                                   // Type
        'details'     => 'https://open-time.net/?q=wordCount',       // Details URL
        'support'     => 'https://github.com/franck-paul/wordCount', // Support URL
        'settings'    => []
    ]
);
