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
use dcNamespace;
use dcNsProcess;
use Exception;

class Install extends dcNsProcess
{
    public static function init(): bool
    {
        static::$init = My::checkContext(My::INSTALL);

        return static::$init;
    }

    public static function process(): bool
    {
        if (!static::$init) {
            return false;
        }

        try {
            $old_version = dcCore::app()->getVersion(My::id());
            if (version_compare((string) $old_version, '3.2', '<')) {
                // Rename settings namespace
                if (dcCore::app()->blog->settings->exists('wordcount')) {
                    dcCore::app()->blog->settings->delNamespace(My::id());
                    dcCore::app()->blog->settings->renNamespace('wordcount', My::id());
                }

                // Change settings names (remove wc_ prefix in them)
                $rename = function (string $name, dcNamespace $settings): void {
                    if ($settings->settingExists('wc_' . $name, true)) {
                        $settings->rename('wc_' . $name, $name);
                    }
                };

                $settings = dcCore::app()->blog->settings->get(My::id());

                $rename('active', $settings);
                $rename('details', $settings);
                $rename('wpm', $settings);
                $rename('autorefresh', $settings);
                $rename('timeout', $settings);
            }

            // Default state is active
            $settings = dcCore::app()->blog->settings->get(My::id());

            $settings->put('active', true, 'boolean', 'Active', false, true);
            $settings->put('details', false, 'boolean', 'Details', false, true);
            $settings->put('wpm', 230, 'integer', 'Average words per minute', false, true);
            $settings->put('autorefresh', true, 'boolean', 'Auto refresh counters', false, true);
            $settings->put('timeout', 60, 'integer', 'Interval between two refresh', false, true);

            return true;
        } catch (Exception $e) {
            dcCore::app()->error->add($e->getMessage());
        }

        return true;
    }
}
