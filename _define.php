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
    'Word Count',
    'Counts characters, words and folios, reading time of entry',
    'Franck Paul',
    '2.0',
    [
        'requires'    => [['core', '2.24']],
        'permissions' => dcCore::app()->auth->makePermissions([
            dcAuth::PERMISSION_USAGE,
            dcAuth::PERMISSION_CONTENT_ADMIN,
        ]),
        'type'     => 'plugin',
        'settings' => [],

        'details'    => 'https://open-time.net/?q=wordCount',
        'support'    => 'https://github.com/franck-paul/wordCount',
        'repository' => 'https://raw.githubusercontent.com/franck-paul/wordCount/master/dcstore.xml',
    ]
);
