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
require_once XOOPS_ROOT_PATH . '/class/xoopsformloader.php';
$GLOBALS['xoopsOption']['template_main'] = 'userpage_edit.tpl';
require_once XOOPS_ROOT_PATH . '/header.php';

$userpageHandler = \XoopsModules\Userpage\Helper::getInstance()->getHandler('Page');
$myts             = \MyTextSanitizer::getInstance();

$op = 'edit';
if (isset($_GET['op'])) {
    $op = $_GET['op'];
} elseif (isset($_POST['op'])) {
    $op = $_POST['op'];
}

$uid = 0;
if (is_object($xoopsUser)) {
    $uid = $xoopsUser->getVar('uid');
} else {
    redirect_header(XOOPS_URL . '/index.php', 2, _USERPAGE_PAGE_NOT_FOUND);
    exit();
}

switch ($op) {
    case 'edit':        // Edit the page
        $xoopsTpl->assign('op', $op);
        $criteria = new \Criteria('up_uid', $uid, '=');
        $cnt      = $userpageHandler->getCount($criteria);
        if ($cnt > 0) {
            $page    = $userpageHandler->getObjects($criteria);
            $pagetbl = $userpageHandler->getObjects($criteria);
            $page    = $pagetbl[0];
        } else {
            $page = $userpageHandler->create(true);
        }
        $xoopsTpl->assign('up_pageid', $page->getVar('up_pageid', 'e'));
        $xoopsTpl->assign('up_title', $page->getVar('up_title', 'e'));
        $xoopsTpl->assign('up_text', $page->getVar('up_text', 'e'));
        $xoopsTpl->assign('up_created', $page->getVar('up_created', 'e'));
        $xoopsTpl->assign('up_hits', $page->getVar('up_hits', 'e'));
        // Page's title
        $xoopsTpl->assign('xoops_pagetitle', strip_tags(_USERPAGE_EDIT) . ' - ' . $myts->htmlSpecialChars($xoopsModule->name()));
        $editor = Utility::getWysiwygForm('Editor', 'up_text', $page->getVar('up_text', 'e'), 15, 60, 'userpage_hidden');
        $xoopsTpl->assign('editor', $editor->render());
        break;
    case 'save':        // Save the page after it was edited
        $criteria = new \Criteria('up_uid', $uid, '=');
        $cnt      = $userpageHandler->getCount($criteria);
        if ($cnt > 0) {
            $creation = false;
            $pagetbl  = $userpageHandler->getObjects($criteria);
            $page     = $pagetbl[0];
            $page->unsetNew();
        } else {
            $creation = true;
            $page     = $userpageHandler->create(true);
            $page->setNew();
        }
        if ($creation) {
            $page->setVar('up_uid', $uid);
            $page->setVar('up_created', time());
            $page->setVar('up_hits', 0);
        }
        $up_title = isset($_POST['up_title']) ? $_POST['up_title'] : '';
        $up_text  = isset($_POST['up_text']) ? $_POST['up_text'] : '';
        $page->setVar('up_title', $up_title);
        $page->setVar('up_text', $up_text);
        if ($userpageHandler->insert($page, true)) {
            Utility::updateCache();        // Remove module's cache
            redirect_header('index.php', 1, _USERPAGE_DB_OK);
        } else {
            redirect_header('index.php', 2, _USERPAGE_DB_PB);
        }
        break;
}
require_once XOOPS_ROOT_PATH . '/footer.php';
