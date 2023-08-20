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
use Dotclear\Core\Process;

class Backend extends Process
{
    public static function init(): bool
    {
        // dead but useful code, in order to have translations
        __('Browser notifications') . __('Display notifications in your web browser');

        return self::status(My::checkContext(My::BACKEND));
    }

    public static function process(): bool
    {
        if (!self::status()) {
            return false;
        }

        dcCore::app()->addBehaviors([
            'adminBeforeUserOptionsUpdate' => BackendBehaviors::adminBeforeUserOptionsUpdate(...),
            'adminPreferencesFormV2'       => BackendBehaviors::adminPreferencesForm(...),

            // On all admin pages
            'adminPageHTMLHead' => BackendBehaviors::adminPageHTMLHead(...),

            // On post and page editing mode
            'adminPostHeaders' => BackendBehaviors::adminPostHeaders(...),
            'adminPageHeaders' => BackendBehaviors::adminPostHeaders(...),

            // Transform error and standard DC notices to notifications
            'adminPageNotificationError' => BackendBehaviors::adminPageNotificationError(...),
            'adminPageNotification'      => BackendBehaviors::adminPageNotification(...),
        ]);

        dcCore::app()->rest->addFunction('notifyMeCheckNewComments', BackendRest::checkNewComments(...));
        dcCore::app()->rest->addFunction('notifyMeCheckCurrentPost', BackendRest::checkCurrentPost(...));

        return true;
    }
}
