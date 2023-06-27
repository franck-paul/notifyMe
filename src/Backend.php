<?php
/**
 * @brief notifyMe, a plugin for Dotclear 2
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

namespace Dotclear\Plugin\notifyMe;

use dcCore;
use dcNsProcess;

class Backend extends dcNsProcess
{
    protected static $init = false; /** @deprecated since 2.27 */
    public static function init(): bool
    {
        static::$init = My::checkContext(My::BACKEND);

        // dead but useful code, in order to have translations
        __('Browser notifications') . __('Display notifications in your web browser');

        return static::$init;
    }

    public static function process(): bool
    {
        if (!static::$init) {
            return false;
        }

        dcCore::app()->addBehaviors([
            'adminBeforeUserOptionsUpdate' => [BackendBehaviors::class, 'adminBeforeUserOptionsUpdate'],
            'adminPreferencesFormV2'       => [BackendBehaviors::class, 'adminPreferencesForm'],

            // On all admin pages
            'adminPageHTMLHead' => [BackendBehaviors::class, 'adminPageHTMLHead'],

            // On post and page editing mode
            'adminPostHeaders' => [BackendBehaviors::class, 'adminPostHeaders'],
            'adminPageHeaders' => [BackendBehaviors::class, 'adminPostHeaders'],

            // Transform error and standard DC notices to notifications
            'adminPageNotificationError' => [BackendBehaviors::class, 'adminPageNotificationError'],
            'adminPageNotification'      => [BackendBehaviors::class, 'adminPageNotification'],
        ]);

        dcCore::app()->rest->addFunction('notifyMeCheckNewComments', [BackendRest::class, 'checkNewComments']);
        dcCore::app()->rest->addFunction('notifyMeCheckCurrentPost', [BackendRest::class, 'checkCurrentPost']);

        return true;
    }
}
