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

use Dotclear\App;
use Dotclear\Core\Process;
use Dotclear\Interface\Core\BlogWorkspaceInterface;
use Exception;

class Install extends Process
{
    public static function init(): bool
    {
        return self::status(My::checkContext(My::INSTALL));
    }

    public static function process(): bool
    {
        if (!self::status()) {
            return false;
        }

        try {
            $old_version = App::version()->getVersion(My::id());
            if (version_compare((string) $old_version, '3.2', '<')) {
                // Rename settings namespace
                if (App::blog()->settings()->exists('wordcount')) {
                    App::blog()->settings()->delWorkspace(My::id());
                    App::blog()->settings()->renWorkspace('wordcount', My::id());
                }

                // Change settings names (remove wc_ prefix in them)
                $rename = function (string $name, BlogWorkspaceInterface $settings): void {
                    if ($settings->settingExists('wc_' . $name, true)) {
                        $settings->rename('wc_' . $name, $name);
                    }
                };

                $settings = My::settings();

                $rename('active', $settings);
                $rename('details', $settings);
                $rename('wpm', $settings);
                $rename('autorefresh', $settings);
                $rename('timeout', $settings);
            }

            // Default state is active
            $settings = My::settings();

            $settings->put('active', true, 'boolean', 'Active', false, true);
            $settings->put('details', false, 'boolean', 'Details', false, true);
            $settings->put('wpm', 230, 'integer', 'Average words per minute', false, true);
            $settings->put('autorefresh', true, 'boolean', 'Auto refresh counters', false, true);
            $settings->put('timeout', 60, 'integer', 'Interval between two refresh', false, true);
        } catch (Exception $e) {
            App::error()->add($e->getMessage());
        }

        return true;
    }
}
