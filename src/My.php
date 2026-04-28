<?php

/**
 * @brief wordCount, a plugin for Dotclear 2
 *
 * @package Dotclear
 * @subpackage Plugins
 *
 * @author Franck Paul and contributors
 *
 * @copyright Franck Paul contact@open-time.net
 * @copyright GPL-2.0 https://www.gnu.org/licenses/gpl-2.0.html
 */
declare(strict_types=1);

namespace Dotclear\Plugin\wordCount;

use Dotclear\Module\MyPlugin;

/**
 * Plugin definitions
 */
class My extends MyPlugin
{
    /**
     * Number of word per minute (230 in English)
     *
     * @var int DEFAULT_WPM
     */
    public const DEFAULT_WPM = 230;

    /**
     * Interval in seconds between two refreshes
     *
     * @var int DEFAULT_INTERVAL
     */
    public const DEFAULT_INTERVAL = 60;

    /*
     * Widget available contexts
     */
    public const WIDGET_POSTS_AND_PAGES = 0;
    public const WIDGET_POSTS_ONLY      = 1;
    public const WIDGET_PAGES_ONLY      = 2;
}
