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
require __DIR__ . '/header.php';
$userpageHandler = \XoopsModules\Userpage\Helper::getInstance()->getHandler('Page');
global $myts;
$myts       = \MyTextSanitizer::getInstance();
$com_itemid = isset($_GET['com_itemid']) ? (int)$_GET['com_itemid'] : 0;
if ($com_itemid > 0) {
    $criteria = new \Criteria('up_pageid', $com_itemid, '=');
    $cnt      = $userpageHandler->getCount($criteria);
    if ($cnt > 0) {
        $pagetbl = $userpageHandler->getObjects($criteria);
        $page    = $pagetbl[0];
        $title   = $page->getVar('up_title');
    } else {
        $title = '';
    }
    $com_replytitle = $title;
    require XOOPS_ROOT_PATH . '/include/comment_new.php';
}
