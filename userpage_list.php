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
$GLOBALS['xoopsOption']['template_main'] = 'userpage_list.tpl';
require_once XOOPS_ROOT_PATH . '/header.php';
require_once XOOPS_ROOT_PATH . '/class/pagenav.php';

$userpageHandler = \XoopsModules\Userpage\Helper::getInstance()->getHandler('Page');
$allowhtml        = Utility::getModuleOption('allowhtml');
$myts             = \MyTextSanitizer::getInstance();

$limit = Utility::getModuleOption('linesperpage');
$xoopsTpl->assign('allowrss', Utility::getModuleOption('allowrss'));
$start   = isset($_GET['start']) ? (int)$_GET['start'] : 0;
$critere = new \Criteria('1', '1', '=');
$critere->setLimit($limit);
$critere->setStart($start);
// tip, replace "up_created" with "up_uid" if you want to sort by user and not by date
$critere->setSort('up_created');
$critere->setOrder('DESC');
$pagescount = $userpageHandler->getCount();
if ($pagescount > $limit) {
    $pagenav = new \XoopsPageNav($pagescount, $limit, $start, 'start', 'op=list');
    $xoopsTpl->assign('pagenav', $pagenav->renderNav());
}

$pages = [];
$pages = $userpageHandler->getObjects($critere);
foreach ($pages as $page) {
    $page->setVar('dohtml', $allowhtml);
    $xoopsTpl->append('pages', $page->toArray());
}
// Page's title
$xoopsTpl->assign('xoops_pagetitle', strip_tags(_USERPAGE_BOOK) . ' - ' . $myts->htmlSpecialChars($xoopsModule->name()));
require_once XOOPS_ROOT_PATH . '/footer.php';
