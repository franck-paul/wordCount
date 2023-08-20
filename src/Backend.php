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

use dcCore;
use Dotclear\Core\Backend\Menus;
use Dotclear\Core\Process;

class Backend extends Process
{
    public static function init(): bool
    {
        // dead but useful code, in order to have translations
        __('Word Count') . __('Counts characters, words and folios, reading time of entry');

        return self::status(My::checkContext(My::BACKEND));
    }

    public static function process(): bool
    {
        if (!self::status()) {
            return false;
        }

        // Add menu item in blog menu
        dcCore::app()->admin->menus[Menus::MENU_BLOG]->addItem(
            __('Word Count'),
            My::manageUrl(),
            My::icons(),
            preg_match(My::urlScheme(), $_SERVER['REQUEST_URI']),
            My::checkContext(My::MENU)
        );

        $settings = dcCore::app()->blog->settings->get(My::id());
        if ($settings->active && $settings->autorefresh) {
            // Register REST methods
            dcCore::app()->rest->addFunction('wordCountGetCounters', BackendRest::getCounters(...));
        }

        dcCore::app()->addBehaviors([
            // Add behaviour callback for post
            'adminPostForm'    => BackendBehaviors::wordCount(...),
            'adminPostHeaders' => BackendBehaviors::adminPostHeaders(...),
            // Add behaviour callback for page
            'adminPageForm'    => BackendBehaviors::wordCount(...),
            'adminPageHeaders' => BackendBehaviors::adminPostHeaders(...),
        ]);

        if (My::checkContext(My::WIDGETS)) {
            dcCore::app()->addBehaviors([
                // Widget
                'initWidgets' => Widgets::initWidgets(...),
            ]);
        }

        return true;
    }
}
