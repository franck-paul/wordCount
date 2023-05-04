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
declare(strict_types=1);

namespace Dotclear\Plugin\wordCount;

use dcAdmin;
use dcAuth;
use dcCore;
use dcNsProcess;
use dcPage;

class Backend extends dcNsProcess
{
    public static function init(): bool
    {
        static::$init = defined('DC_CONTEXT_ADMIN');

        // dead but useful code, in order to have translations
        __('Word Count') . __('Counts characters, words and folios, reading time of entry');

        return static::$init;
    }

    public static function process(): bool
    {
        if (!static::$init) {
            return false;
        }

        // Add menu item in blog menu
        dcCore::app()->menu[dcAdmin::MENU_BLOG]->addItem(
            __('Word Count'),
            'plugin.php?p=wordCount',
            [urldecode(dcPage::getPF(My::id() . '/icon.svg')), urldecode(dcPage::getPF(My::id() . '/icon-dark.svg'))],
            preg_match('/plugin.php\?p=wordCount(&.*)?$/', $_SERVER['REQUEST_URI']),
            dcCore::app()->auth->check(dcCore::app()->auth->makePermissions([
                dcAuth::PERMISSION_CONTENT_ADMIN,
            ]), dcCore::app()->blog->id)
        );

        if (dcCore::app()->blog->settings->wordcount->wc_active && dcCore::app()->blog->settings->wordcount->wc_autorefresh) {
            // Register REST methods
            dcCore::app()->rest->addFunction('wordCountGetCounters', [BackendRest::class, 'getCounters']);
        }

        dcCore::app()->addBehaviors([
            // Add behaviour callback for post
            'adminPostForm'    => [BackendBehaviors::class, 'wordCount'],
            'adminPostHeaders' => [BackendBehaviors::class, 'adminPostHeaders'],
            // Add behaviour callback for page
            'adminPageForm'    => [BackendBehaviors::class, 'wordCount'],
            'adminPageHeaders' => [BackendBehaviors::class, 'adminPostHeaders'],

            // Widget
            'initWidgets' => [Widgets::class, 'initWidgets'],
        ]);

        return true;
    }
}
