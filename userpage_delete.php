<?php
/**
 * ****************************************************************************
 * userpage - MODULE FOR XOOPS
 * Copyright (c) Hervé Thouzard of Instant Zero (http://www.instant-zero.com)
 *
 * You may not change or alter any portion of this comment or credits
 * of supporting developers from this source code or any supporting source code
 * which is considered copyrighted (c) material of the original comment or credit authors.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * @copyright       Hervé Thouzard of Instant Zero (http://www.instant-zero.com)
 * @license         http://www.fsf.org/copyleft/gpl.html GNU public license
 * @package         userpage
 * @author          Hervé Thouzard of Instant Zero (http://www.instant-zero.com)
 *
 * ****************************************************************************
 */

use XoopsModules\Userpage\Utility;


require __DIR__ . '/header.php';
require_once XOOPS_ROOT_PATH . '/header.php';

$userpageHandler = \XoopsModules\Userpage\Helper::getInstance()->getHandler('Page');
$uid              = $id = 0;
$res              = false;

if (is_object($xoopsUser) && $xoopsUser->isAdmin($xoopsModule->getVar('mid'))) {
    $uid = $xoopsUser->getVar('uid');
} else {
    header('Location: userpage_list.php');
    exit;
}

if (isset($_GET['id'])) {
    $id   = (int)$_GET['id'];
    $page = $userpageHandler->get($id);
    if (is_object($page)) {
        $res = $userpageHandler->delete($page, true);
    }
}
if ($res) {
    redirect_header('index.php', 2, _USERPAGE_DB_OK);
    exit();
}
redirect_header('index.php', 4, _ERRORS);
exit();

require_once XOOPS_ROOT_PATH . '/footer.php';
