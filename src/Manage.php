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
use dcPage;
use Dotclear\Helper\Html\Form\Checkbox;
use Dotclear\Helper\Html\Form\Div;
use Dotclear\Helper\Html\Form\Fieldset;
use Dotclear\Helper\Html\Form\Form;
use Dotclear\Helper\Html\Form\Label;
use Dotclear\Helper\Html\Form\Legend;
use Dotclear\Helper\Html\Form\Number;
use Dotclear\Helper\Html\Form\Para;
use Dotclear\Helper\Html\Form\Submit;
use Dotclear\Helper\Html\Html;
use Dotclear\Helper\Network\Http;
use Exception;

class Manage extends dcNsProcess
{
    /**
     * Initializes the page.
     */
    public static function init(): bool
    {
        // Manageable only by super-admin
        static::$init = defined('DC_CONTEXT_ADMIN')
            && My::phpCompliant();

        return static::$init;
    }

    /**
     * Processes the request(s).
     */
    public static function process(): bool
    {
        if (!static::$init) {
            return false;
        }

        $settings = dcCore::app()->blog->settings->wordcount;

        // Getting current parameters
        $wc_active      = (bool) $settings->wc_active;
        $wc_details     = (bool) $settings->wc_details;
        $wc_wpm         = (int) ($settings->wc_wpm ?? 230);
        $wc_autorefresh = (bool) $settings->wc_autorefresh;
        $wc_interval    = (int) ($settings->wc_interval ?? 60);

        // Saving new configuration
        if (!empty($_POST['saveconfig'])) {
            try {
                $wc_active   = (empty($_POST['active'])) ? false : true;
                $wc_details  = (empty($_POST['details'])) ? false : true;
                $wc_wpm      = (int) $_POST['wpm'];
                $wc_details  = (empty($_POST['autorefresh'])) ? false : true;
                $wc_interval = (int) $_POST['interval'];

                $settings->put('wc_active', $wc_active, 'boolean');
                $settings->put('wc_details', $wc_details, 'boolean');
                $settings->put('wc_wpm', ($wc_wpm ?: 230), 'integer');
                $settings->put('wc_autorefresh', $wc_autorefresh, 'boolean');
                $settings->put('wc_interval', ($wc_interval ?: 60), 'integer');

                dcCore::app()->blog->triggerBlog();
                dcPage::addSuccessNotice(__('Configuration successfully updated.'));

                Http::redirect(dcCore::app()->admin->getPageURL());
            } catch (Exception $e) {
                dcCore::app()->error->add($e->getMessage());
            }
        }

        // Get updated parameters
        dcCore::app()->admin->wc_active      = $wc_active;
        dcCore::app()->admin->wc_details     = $wc_details;
        dcCore::app()->admin->wc_wpm         = $wc_wpm;
        dcCore::app()->admin->wc_autorefresh = $wc_autorefresh;
        dcCore::app()->admin->wc_interval    = $wc_interval;

        return true;
    }

    /**
     * Renders the page.
     */
    public static function render(): void
    {
        if (!static::$init) {
            return;
        }

        dcPage::openModule(__('Word Count'));

        echo dcPage::breadcrumb(
            [
                Html::escapeHTML(dcCore::app()->blog->name) => '',
                __('Word Count')                            => '',
            ]
        );
        echo dcPage::notices();

        echo (new Div('options'))
            ->items([
                (new Form('options-form'))
                ->action(dcCore::app()->admin->getPageURL())
                ->method('post')
                ->fields([
                    (new Para())->items([
                        (new Checkbox('active', dcCore::app()->admin->wc_active))
                            ->value(1)
                            ->label((new Label(__('Enable Word Count for this blog'), Label::INSIDE_TEXT_AFTER))),
                    ]),
                    (new Fieldset())
                        ->legend((new Legend(__('Options'))))
                        ->fields([
                            (new Para())->items([
                                (new Checkbox('details', dcCore::app()->admin->wc_details))
                                    ->value(1)
                                    ->label((new Label(__('Show details (excerpt and content)'), Label::INSIDE_TEXT_AFTER))),
                            ]),
                            (new Para())->items([
                                (new Number('wpm', 1, 9999, dcCore::app()->admin->wc_wpm))
                                    ->label((new Label(__('Average words per minute (reading, usually 230):'), Label::INSIDE_TEXT_BEFORE))),
                            ]),
                            (new Para())->items([
                                (new Checkbox('autorefresh', dcCore::app()->admin->wc_autorefresh))
                                    ->value(1)
                                    ->label((new Label(__('Auto refresh counters'), Label::INSIDE_TEXT_AFTER))),
                            ]),
                            (new Para())->items([
                                (new Number('interval', 15, 999, (int) dcCore::app()->admin->wc_interval))
                                    ->label((new Label(__('Autorefresh interval in seconds (usually 60):'), Label::INSIDE_TEXT_BEFORE))),
                            ]),
                        ]),
                    (new Para())->items([
                        (new Submit(['saveconfig'], __('Save')))
                            ->accesskey('s'),
                        dcCore::app()->formNonce(false),
                    ]),
                ]),
            ])
            ->render();

        dcPage::closeModule();
    }
}
