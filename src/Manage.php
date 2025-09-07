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
use Dotclear\Core\Backend\Notices;
use Dotclear\Core\Backend\Page;
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
use Dotclear\Helper\Process\TraitProcess;
use Exception;

class Manage
{
    use TraitProcess;

    /**
     * Initializes the page.
     */
    public static function init(): bool
    {
        return self::status(My::checkContext(My::MANAGE));
    }

    /**
     * Processes the request(s).
     */
    public static function process(): bool
    {
        if (!self::status()) {
            return false;
        }

        // Saving new configuration
        if (!empty($_POST['saveconfig'])) {
            try {
                $settings = My::settings();

                $active      = !empty($_POST['active']);
                $details     = !empty($_POST['details']);
                $wpm         = (int) $_POST['wpm'];
                $autorefresh = !empty($_POST['autorefresh']);
                $interval    = (int) $_POST['interval'];

                $settings->put('active', $active, 'boolean');
                $settings->put('details', $details, 'boolean');
                $settings->put('wpm', ($wpm ?: 230), 'integer');
                $settings->put('autorefresh', $autorefresh, 'boolean');
                $settings->put('interval', ($interval ?: 60), 'integer');

                App::blog()->triggerBlog();
                Notices::addSuccessNotice(__('Configuration successfully updated.'));
                My::redirect();
            } catch (Exception $e) {
                App::error()->add($e->getMessage());
            }
        }

        return true;
    }

    /**
     * Renders the page.
     */
    public static function render(): void
    {
        if (!self::status()) {
            return;
        }

        // Getting current parameters
        $settings = My::settings();

        $active      = (bool) $settings->active;
        $details     = (bool) $settings->details;
        $wpm         = (int) ($settings->wpm ?? 230);
        $autorefresh = (bool) $settings->autorefresh;
        $interval    = (int) ($settings->interval ?? 60);

        Page::openModule(My::name());

        echo Page::breadcrumb(
            [
                Html::escapeHTML(App::blog()->name()) => '',
                __('Word Count')                      => '',
            ]
        );
        echo Notices::getNotices();

        echo (new Div('options'))
            ->items([
                (new Form('options-form'))
                ->action(App::backend()->getPageURL())
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
                                (new Number('interval', 15, 999, $interval))
                                    ->label((new Label(__('Autorefresh interval in seconds (usually 60):'), Label::INSIDE_TEXT_BEFORE))),
                            ]),
                        ]),
                    (new Para())->items([
                        (new Submit(['saveconfig'], __('Save')))
                            ->accesskey('s'),
                        ... My::hiddenFields(),
                    ]),
                ]),
            ])
            ->render();

        Page::closeModule();
    }
}
