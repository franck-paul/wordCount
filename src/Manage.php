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
use Exception;

class Manage extends dcNsProcess
{
    /**
     * Initializes the page.
     */
    public static function init(): bool
    {
        static::$init = My::checkContext(My::MANAGE);

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

        // Saving new configuration
        if (!empty($_POST['saveconfig'])) {
            try {
                $settings = dcCore::app()->blog->settings->get(My::id());

                $active      = (empty($_POST['active'])) ? false : true;
                $details     = (empty($_POST['details'])) ? false : true;
                $wpm         = (int) $_POST['wpm'];
                $autorefresh = (empty($_POST['autorefresh'])) ? false : true;
                $interval    = (int) $_POST['interval'];

                $settings->put('active', $active, 'boolean');
                $settings->put('details', $details, 'boolean');
                $settings->put('wpm', ($wpm ?: 230), 'integer');
                $settings->put('autorefresh', $autorefresh, 'boolean');
                $settings->put('interval', ($interval ?: 60), 'integer');

                dcCore::app()->blog->triggerBlog();
                dcPage::addSuccessNotice(__('Configuration successfully updated.'));
                dcCore::app()->adminurl->redirect('admin.plugin.' . My::id());
            } catch (Exception $e) {
                dcCore::app()->error->add($e->getMessage());
            }
        }

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

        // Getting current parameters
        $settings = dcCore::app()->blog->settings->get(My::id());

        $active      = (bool) $settings->active;
        $details     = (bool) $settings->details;
        $wpm         = (int) ($settings->wpm ?? 230);
        $autorefresh = (bool) $settings->autorefresh;
        $interval    = (int) ($settings->interval ?? 60);

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
                        (new Checkbox('active', $active))
                            ->value(1)
                            ->label((new Label(__('Enable Word Count for this blog'), Label::INSIDE_TEXT_AFTER))),
                    ]),
                    (new Fieldset())
                        ->legend((new Legend(__('Options'))))
                        ->fields([
                            (new Para())->items([
                                (new Checkbox('details', $details))
                                    ->value(1)
                                    ->label((new Label(__('Show details (excerpt and content)'), Label::INSIDE_TEXT_AFTER))),
                            ]),
                            (new Para())->items([
                                (new Number('wpm', 1, 9999, $wpm))
                                    ->label((new Label(__('Average words per minute (reading, usually 230):'), Label::INSIDE_TEXT_BEFORE))),
                            ]),
                            (new Para())->items([
                                (new Checkbox('autorefresh', $autorefresh))
                                    ->value(1)
                                    ->label((new Label(__('Auto refresh counters'), Label::INSIDE_TEXT_AFTER))),
                            ]),
                            (new Para())->items([
                                (new Number('interval', 15, 999, (int) $interval))
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
