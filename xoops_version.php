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
defined('XOOPS_ROOT_PATH') || die('XOOPS root path not defined');

$modversion['version']       = 2.0;
$modversion['module_status'] = 'Beta 1';
$modversion['release_date']  = '2019/05/30';
$modversion['name']          = _MI_USERPAGE_NAME;
$modversion['description']   = _MI_USERPAGE_DESC;
$modversion['credits']       = 'DefianceB0y, Riosoft, Shine, Gibaphp, Feichtl and Calidro (and all the others) for the quick translations !';
$modversion['author']        = 'Instant Zero - http://xoops.instant-zero.com';
$modversion['help']          = 'page=help';
$modversion['license']       = 'GNU GPL 2.0 or later';
$modversion['license_url']   = 'www.gnu.org/licenses/gpl-2.0.html';
$modversion['official']      = 0;
$modversion['image']         = 'assets/images/logoModule.png';
$modversion['dirname']       = basename(__DIR__);
$modversion['modicons16']    = 'assets/images/icons/16';
$modversion['modicons32']    = 'assets/images/icons/32';
//about
$modversion['module_website_url']  = 'www.xoops.org';
$modversion['module_website_name'] = 'XOOPS';
$modversion['min_php']             = '5.6';
$modversion['min_xoops']           = '2.5.10';
$modversion['min_admin']           = '1.2';
$modversion['min_db']              = ['mysql' => '5.5'];

$modversion['sqlfile']['mysql'] = 'sql/mysql.sql';
// Tables created by sql file (without prefix!)
$modversion['tables'][0] = 'userpage';

// Admin things
$modversion['hasAdmin']    = 1;
$modversion['system_menu'] = 1;
$modversion['adminindex']  = 'admin/index.php';
$modversion['adminmenu']   = 'admin/menu.php';

// ------------------- Help files ------------------- //
$modversion['helpsection'] = [
    ['name' => _MI_USERPAGE_OVERVIEW, 'link' => 'page=help'],
    ['name' => _MI_USERPAGE_DISCLAIMER, 'link' => 'page=disclaimer'],
    ['name' => _MI_USERPAGE_LICENSE, 'link' => 'page=license'],
    ['name' => _MI_USERPAGE_SUPPORT, 'link' => 'page=support'],
];

// ------------------- Templates ------------------- //
$modversion['templates'] = [
    ['file' => 'userpage_index.tpl', 'description' => 'Show and edit a user page and show a list of all pages'],
    ['file' => 'userpage_rss.tpl', 'description' => 'RSS Feed'],
    ['file' => 'userpage_edit.tpl', 'description' => "Form used to edit a user's page"],
    ['file' => 'userpage_list.tpl', 'description' => "Show a list of user's page"],
];

// ------------------- Blocks ------------------- //
$modversion['blocks'][] = [
    'file'        => 'userpage_last.php',
    'name'        => _MI_USERPAGE_BNAME1,
    'description' => 'Show last pages',
    'show_func'   => 'b_userpage_last_show',
    'edit_func'   => 'b_userpage_last_edit',
    'options'     => '10|30',    // 10=Items count, 30=Title's length
    'template'    => 'userpage_block_last.tpl',
];

$modversion['blocks'][] = [
    'file'        => 'userpage_top.php',
    'name'        => _MI_USERPAGE_BNAME2,
    'description' => 'Show most viewed pages',
    'show_func'   => 'b_userpage_top_show',
    'edit_func'   => 'b_userpage_top_edit',
    'options'     => '10|30',    // 10=Items count, 30=Title's length
    'template'    => 'userpage_block_top.tpl',
];

$modversion['blocks'][] = [
    'file'        => 'userpage_random.php',
    'name'        => _MI_USERPAGE_BNAME3,
    'description' => 'Show random pages',
    'show_func'   => 'b_userpage_random_show',
    'edit_func'   => 'b_userpage_random_edit',
    'options'     => '10|30',    // 10=Items count, 30=Title's length
    'template'    => 'userpage_block_random.tpl',
];

// Menu
$modversion['hasMain']        = 1;
$modversion['sub'][1]['name'] = _MI_USERPAGE_MENU1;
$modversion['sub'][1]['url']  = 'userpage_list.php';

// Search
$modversion['hasSearch']      = 1;
$modversion['search']['file'] = 'include/search.inc.php';
$modversion['search']['func'] = 'userpage_search';

// Comments
$modversion['hasComments']          = 1;
$modversion['comments']['pageName'] = 'index.php';
$modversion['comments']['itemName'] = 'page_id';

/**
 * Allow html ?
 */
$modversion['config'][] = [
    'name'        => 'allowhtml',
    'title'       => '_MI_USERPAGE_OPT0',
    'description' => '_MI_USERPAGE_OPT0_DSC',
    'formtype'    => 'yesno',
    'valuetype'   => 'int',
    'default'     => 0,
];

/**
 * Allow RSS Feeds ?
 */
$modversion['config'][] = [
    'name'        => 'allowrss',
    'title'       => '_MI_USERPAGE_OPT1',
    'description' => '_MI_USERPAGE_OPT1_DSC',
    'formtype'    => 'yesno',
    'valuetype'   => 'int',
    'default'     => 1,
];

/**
 * Date's format. If you don't specify anything then the default date's format will be used
 */
$modversion['config'][] = [
    'name'        => 'dateformat',
    'title'       => '_MI_USERPAGE_OPT3',
    'description' => '_MI_USERPAGE_OPT3_DSC',
    'formtype'    => 'textbox',
    'valuetype'   => 'text',
    'default'     => '',
];

/**
 * Number of characters to use in the RSS feed
 */
$modversion['config'][] = [
    'name'        => 'rsslength',
    'title'       => '_MI_USERPAGE_OPT4',
    'description' => '_MI_USERPAGE_OPT4_DSC',
    'formtype'    => 'textbox',
    'valuetype'   => 'int',
    'default'     => 200,
];

/**
 * Number of lines per page
 */
$modversion['config'][] = [
    'name'        => 'linesperpage',
    'title'       => '_MI_USERPAGE_OPT5',
    'description' => '_MI_USERPAGE_OPT5_DSC',
    'formtype'    => 'textbox',
    'valuetype'   => 'int',
    'default'     => 10,
];

/**
 * Editor to use
 */
xoops_load('xoopseditorhandler');
$editorHandler          = \XoopsEditorHandler::getInstance();
$modversion['config'][] = [
    'name'        => 'usekiovi',
    'title'       => '_MI_USERPAGE_OPT6',
    'description' => '_MI_USERPAGE_OPT6_DSC',
    'formtype'    => 'select',
    'valuetype'   => 'text',
    'default'     => 'dhtmltextarea',
    'options'     => array_flip($editorHandler->getList()),
];

/**
 * Allow html ?
 */
$modversion['config'][] = [
    'name'        => 'url_rewriting',
    'title'       => '_MI_USERPAGE_URL_REWRITING',
    'description' => '',
    'formtype'    => 'yesno',
    'valuetype'   => 'int',
    'default'     => 0,
];
// Notifications
$modversion['hasNotification'] = 0;
