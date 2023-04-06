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

use dcPage;

class Helper
{
    public static function NotifyBrowser($message, $title = 'Dotclear')
    {
        return dcPage::jsJson('notify_me_msg_' . time(), [
            'message' => str_replace("\n", '. ', $message),
            'title'   => $title,
            'silent'  => false,
        ]);
    }
}
