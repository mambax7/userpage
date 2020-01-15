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
// If you want to limit access to this page to registred users only, uncomment the following lines :
/*
if (!is_object($xoopsUser)) {   // Only for registred users
    redirect_header(XOOPS_URL.'/index.php', 2, _ERRORS);
    exit();
}
*/
$userpageHandler = \XoopsModules\Userpage\Helper::getInstance()->getHandler('Page');
$uid             = 0;
if (is_object($xoopsUser)) {
    $uid = $xoopsUser->getVar('uid');
}

$page_id = isset($_GET['page_id']) ? (int)$_GET['page_id'] : 0;
if (empty($page_id)) {    // Search for current user's page
    $criteria = new \Criteria('up_uid', $uid, '=');
} else {    // Search for a specific user page
    $criteria = new \Criteria('up_pageid', $page_id, '=');
}

$cnt = $userpageHandler->getCount($criteria);
if ($cnt > 0) {
    $pagetbl = $userpageHandler->getObjects($criteria);
    $page    = $pagetbl[0];
} else {    // Page not found
    redirect_header(XOOPS_URL . '/index.php', 2, _USERPAGE_PAGE_NOT_FOUND);
    exit();
}

$page->setVar('dohtml', Utility::getModuleOption('allowhtml'));
$myts = \MyTextSanitizer::getInstance();
echo '<!DOCTYPE HTML>';
echo '<html><head>';
echo '<meta http-equiv="Content-Type" content="text/html; charset=' . _CHARSET . '">';
echo '<title>' . _USERPAGE_PAGE_OF . $page->uname() . ' - ' . $myts->htmlSpecialChars(_USERPAGE_PRINTABLE) . ' - ' . $xoopsConfig['sitename'] . '</title>';
echo '<meta name="AUTHOR" content="' . $xoopsConfig['sitename'] . '">';
echo '<meta name="COPYRIGHT" content="Copyright (c) 2006 by ' . $xoopsConfig['sitename'] . '">';
echo '<meta name="DESCRIPTION" content="' . $xoopsConfig['slogan'] . '">';
echo '<meta name="GENERATOR" content="USERPAGE By Instant Zero">';
echo '<body bgcolor="#ffffff" text="#000000" onload="window.print()">
    <table border="0"><tr><td align="center">
    <table border="0" width="100%" cellpadding="0" cellspacing="1" bgcolor="#000000"><tr><td>
    <table border="0" width="100%" cellpadding="20" cellspacing="1" bgcolor="#ffffff"><tr><td align="center">
    <img src="' . XOOPS_URL . '/images/logo.gif" border="0" alt=""><br><br>
    <h3>' . _USERPAGE_PAGE_OF . $page->uname() . '</h3>';
echo '<tr valign="top" style="font:12px;"><td>';
echo "<table border='0' width='100%' align='center'>";
echo '<tr><td><b>' . $page->getVar('up_title') . '</b></td></tr>';
echo '<tr><td>' . $page->getVar('up_text') . '</td></tr>';
echo '</table>';
echo '</td></tr></table></td></tr></table><br><br>';
printf(_USERPAGE_THISCOMESFROM, $xoopsConfig['sitename']);
echo '<br><a href="' . XOOPS_URL . '/">' . XOOPS_URL . '</a><br><br>' . _USERPAGE_URLFORPAGE . ' <br><a href="' . $page->getURL() . '">' . $page->getURL() . '</a></td></tr></table></body></html>';
