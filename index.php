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
$GLOBALS['xoopsOption']['template_main'] = 'userpage_index.tpl';
require_once XOOPS_ROOT_PATH . '/header.php';

$userpageHandler =  \XoopsModules\Userpage\Helper::getInstance()->getHandler('Page');;
$allowhtml        = Utility::getModuleOption('allowhtml');
$myts             = \MyTextSanitizer::getInstance();

$is_admin = false;
$uid      = 0;
if (is_object($xoopsUser)) {
    $uid = $xoopsUser->getVar('uid');
    if ($xoopsUser->isAdmin($xoopsModule->getVar('mid'))) {
        $is_admin = true;
    }
    $xoopsTpl->assign('confirm_delete', Utility::javascriptLinkConfirm(_USERPAGE_ARE_YOU_SURE));
} else {
    if (!isset($_GET['page_id'])) {
        header('Location: userpage_list.php');
    }
}

$xoopsTpl->assign('is_admin', $is_admin);
$page_id = 0;
if (isset($_GET['page_id'])) {
    $page_id = (int)$_GET['page_id'];
}

$xoopsTpl->assign('allowrss', Utility::getModuleOption('allowrss'));
if (empty($page_id)) {    // Show current user's page
    $xoopsTpl->assign('currentuser', true);
    $criteria = new \Criteria('up_uid', $uid, '=');
    $cnt      = $userpageHandler->getCount($criteria);
    if ($cnt > 0) {
        $pagetbl = $userpageHandler->getObjects($criteria);
        $page    = $pagetbl[0];
    } else {
        $page = $userpageHandler->create(true);
    }
} else {    // Shows a user's page
    // Is this the current user's page ?
    $xoopsTpl->assign('currentuser', false);
    $criteria = new \Criteria('up_pageid', $page_id, '=');
    $cnt      = $userpageHandler->getCount($criteria);
    if ($cnt > 0) {
        $pagetbl = $userpageHandler->getObjects($criteria);
        $page    = $pagetbl[0];
        if ($page->getVar('up_uid') == $uid) {
            $xoopsTpl->assign('currentuser', true);
        }
    } else {    // Page not found
        redirect_header(XOOPS_URL . '/index.php', 2, _USERPAGE_PAGE_NOT_FOUND);
        exit();
    }
}
$page->setVar('dohtml', $allowhtml);        // Set html

if (0 != $page->getVar('up_pageid')) {
    $xoopsTpl->assign('mail_cmd', 'mailto:?subject=' . sprintf(_USERPAGE_INTARTICLE, $xoopsConfig['sitename']) . '&amp;body=' . sprintf(_USERPAGE_INTARTFOUND, $xoopsConfig['sitename']) . ':  ' . XOOPS_URL . '/modules/userpage/index.php?page_id=' . $page->getVar('up_pageid'));
    // Update counter (only if the user is not the owner and if the page exists)
    if ($uid != $page->getVar('up_uid')) {
        $userpageHandler->UpdateCounter($page->getVar('up_pageid'));
    }
} else {
    $xoopsTpl->assign('mail_cmd', '');
}
$xoopsTpl->assign('up_pageid', $page->getVar('up_pageid'));
$xoopsTpl->assign('up_title', $page->getVar('up_title'));
$xoopsTpl->assign('up_text', $page->getVar('up_text'));
$xoopsTpl->assign('up_created', $page->getVar('up_created'));
$xoopsTpl->assign('up_uid', $page->getVar('up_uid'));

$userName  = '';
$page_user = null;
$page_user = new \XoopsUser($page->getVar('up_uid'));
if (is_object($page_user)) {
    $userName = $page_user->getVar('uname');
    $xoopsTpl->assign('user_avatar', XOOPS_UPLOAD_URL . '/' . $page_user->getVar('user_avatar'));
    $xoopsTpl->assign('user_name', $userName);
    $xoopsTpl->assign('user_uname', $page_user->getVar('uname'));
    $xoopsTpl->assign('user_email', $page_user->getVar('email'));
    $xoopsTpl->assign('user_url', $page_user->getVar('url'));
    $xoopsTpl->assign('user_from', $page_user->getVar('user_from'));
    $xoopsTpl->assign('user_sig', $page_user->getVar('user_sig'));
}
$xoopsTpl->assign('up_uid', $page->getVar('up_uid'));

if (0 != $page->getVar('up_created')) {
    $xoopsTpl->assign('up_dateformated', formatTimestamp($page->getVar('up_created'), Utility::getModuleOption('dateformat')));
} else {
    $xoopsTpl->assign('up_dateformated', '');
}
$xoopsTpl->assign('up_hits', $page->getVar('up_hits'));

$pagetitle        = strip_tags($page->getVar('up_title')) . ' - ' . $userName . ' - ' . $myts->htmlSpecialChars($xoopsModule->name());
$meta_keywords    = Utility::createMetaKeywords(strip_tags($page->getVar('up_title')) . ' ' . strip_tags($page->getVar('up_text')));
$meta_description = strip_tags($page->getVar('up_title'));

Utility::setMetas($pagetitle, $meta_description, $meta_keywords);

if (!empty($page_id)) {
    require_once XOOPS_ROOT_PATH . '/include/comment_view.php';
}
require_once XOOPS_ROOT_PATH . '/footer.php';
