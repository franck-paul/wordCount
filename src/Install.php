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
use dcNsProcess;
use Exception;

class Install extends dcNsProcess
{
    public static function init(): bool
    {
        static::$init = defined('DC_CONTEXT_ADMIN')
            && My::phpCompliant()
            && dcCore::app()->newVersion(My::id(), dcCore::app()->plugins->moduleInfo(My::id(), 'version'));

        return static::$init;
    }

    public static function process(): bool
    {
        if (!static::$init) {
            return false;
        }

        try {
            // Default state is active
            $settings = dcCore::app()->blog->settings->wordcount;

            $settings->put('wc_active', true, 'boolean', 'Active', false, true);
            $settings->put('wc_details', false, 'boolean', 'Details', false, true);
            $settings->put('wc_wpm', 230, 'integer', 'Average words per minute', false, true);
            $settings->put('wc_autorefresh', true, 'boolean', 'Auto refresh counters', false, true);
            $settings->put('wc_timeout', 60, 'integer', 'Interval between two refresh', false, true);

            return true;
        } catch (Exception $e) {
            dcCore::app()->error->add($e->getMessage());
        }

        return true;
    }
}
