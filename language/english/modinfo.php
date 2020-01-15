<?php
//  ------------------------------------------------------------------------ //
//                      USERPAGE - MODULE FOR XOOPS 2                        //
//                  Copyright (c) 2005-2006 Instant Zero                     //
//                     <http://xoops.instant-zero.com>                      //
// ------------------------------------------------------------------------- //
//  This program is free software; you can redistribute it and/or modify     //
//  it under the terms of the GNU General Public License as published by     //
//  the Free Software Foundation; either version 2 of the License, or        //
//  (at your option) any later version.                                      //
//                                                                           //
//  You may not change or alter any portion of this comment or credits       //
//  of supporting developers from this source code or any supporting         //
//  source code which is considered copyrighted (c) material of the          //
//  original comment or credit authors.                                      //
//                                                                           //
//  This program is distributed in the hope that it will be useful,          //
//  but WITHOUT ANY WARRANTY; without even the implied warranty of           //
//  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the            //
//  GNU General Public License for more details.                             //
//                                                                           //
//  You should have received a copy of the GNU General Public License        //
//  along with this program; if not, write to the Free Software              //
//  Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307 USA //
//  ------------------------------------------------------------------------ //

define('_MI_USERPAGE_NAME', 'UserPage');
define('_MI_USERPAGE_DESC', 'Enable your registred users to create their own page');
define('_MI_USERPAGE_ADMMENU1', 'Pages List');
define('_MI_USERPAGE_OPT0', 'Enable html ?');
define('_MI_USERPAGE_OPT0_DSC', 'Select if users can use html');
define('_MI_USERPAGE_OPT1', 'Enable RSS Feed ?');
define('_MI_USERPAGE_OPT1_DSC', '');
define('_MI_USERPAGE_OPT3', "Date's format");
define('_MI_USERPAGE_OPT3_DSC', "See the <a href='http://fr3.php.net/manual/fr/function.date.php'>Php</a> date function for more informations");
define('_MI_USERPAGE_OPT4', 'Number of characters to display in RSS Feed ?');
define('_MI_USERPAGE_OPT4_DSC', 'Select the number of visible characters in the RSS Feed');
define('_MI_USERPAGE_OPT5', 'Number of lines per page (in the pages listing)');
define('_MI_USERPAGE_OPT5_DSC', 'Select the number of lines in the user side');
//define('_MI_USERPAGE_OPT6',"Use kiovi ?");
// Changed in version 1.1
define('_MI_USERPAGE_OPT6', 'Editor to use ?');
//define('_MI_USERPAGE_OPT6_DSC',"If you set this option to true, don't forget to set the first option (Enable html) to true too");
// Changed in version 1.1
define('_MI_USERPAGE_OPT6_DSC', "Select the editor to use and if necessary, don't forget to set the first option (Enable html) to true");
define('_MI_USERPAGE_BNAME1', 'Last created pages');
define('_MI_USERPAGE_BNAME2', 'Most viewed pages');
define('_MI_USERPAGE_BNAME3', 'Random pages');
define('_MI_USERPAGE_MENU1', 'Pages List');

// Added in version 1.1
define('_MI_USERPAGE_FORM_COMPACT', 'Compact');
define('_MI_USERPAGE_FORM_DHTML', 'DHTML');
define('_MI_USERPAGE_FORM_SPAW', 'Spaw Editor');
define('_MI_USERPAGE_FORM_HTMLAREA', 'HtmlArea Editor');
define('_MI_USERPAGE_FORM_FCK', 'FCK Editor');
define('_MI_USERPAGE_FORM_KOIVI', 'Koivi Editor');
define('_MI_USERPAGE_FORM_TINYEDITOR', 'TinyEditor');

// Added in version 1.3
define('_MI_USERPAGE_URL_REWRITING', 'Use URL Rewriting?');

//Menu
define('_MI_USERPAGE_MENU_HOME', 'Home');
define('_MI_USERPAGE_MENU_01', 'Admin');
define('_MI_USERPAGE_MENU_ABOUT', 'About');

//Help
define('_MI_USERPAGE_DIRNAME', basename(dirname(dirname(__DIR__))));
define('_MI_USERPAGE_HELP_HEADER', __DIR__ . '/help/helpheader.tpl');
define('_MI_USERPAGE_BACK_2_ADMIN', 'Back to Administration of ');
define('_MI_USERPAGE_OVERVIEW', 'Overview');

//define('_MI_USERPAGE_HELP_DIR', __DIR__);

//help multi-page
define('_MI_USERPAGE_DISCLAIMER', 'Disclaimer');
define('_MI_USERPAGE_LICENSE', 'License');
define('_MI_USERPAGE_SUPPORT', 'Support');
