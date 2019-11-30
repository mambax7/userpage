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

/**
 * @param int    $currentoption
 * @param string $breadcrumb
 */
function userpage_adminmenu($currentoption = 0, $breadcrumb = '')
{
    /* Nice buttons styles */
    echo "
        <style type='text/css'>
        #buttontop { float:left; width:100%; background: #e7e7e7; font-size:93%; line-height:normal; border-top: 1px solid black; border-left: 1px solid black; border-right: 1px solid black; margin: 0; }
        #buttonbar { float:left; width:100%; background: #e7e7e7 url('" . XOOPS_URL . "/modules/userpage/assets/images/bg.png') repeat-x left bottom; font-size:93%; line-height:normal; border-left: 1px solid black; border-right: 1px solid black; margin-bottom: 12px; }
        #buttonbar ul { margin:0; margin-top: 15px; padding:10px 10px 0; list-style:none; }
        #buttonbar li { display:inline; margin:0; padding:0; }
        #buttonbar a { float:left; background:url('" . XOOPS_URL . "/modules/userpage/assets/images/left_both.png') no-repeat left top; margin:0; padding:0 0 0 9px; border-bottom:1px solid #000; text-decoration:none; }
        #buttonbar a span { float:left; display:block; background:url('" . XOOPS_URL . "/modules/userpage/assets/images/right_both.png') no-repeat right top; padding:5px 15px 4px 6px; font-weight:bold; color:#765; }
        /* Commented Backslash Hack hides rule from IE5-Mac \*/
        #buttonbar a span {float:none;}
        /* End IE5-Mac hack */
        #buttonbar a:hover span { color:#333; }
        #buttonbar #current a { background-position:0 -150px; border-width:0; }
        #buttonbar #current a span { background-position:100% -150px; padding-bottom:5px; color:#333; }
        #buttonbar a:hover { background-position:0% -150px; }
        #buttonbar a:hover span { background-position:100% -150px; }
        </style>
    ";
    global $xoopsModule, $xoopsConfig;

    $tblColors = ['', '', '', '', ''];
    if ($currentoption >= 0) {
        $tblColors[$currentoption] = 'current';
    }

    if (file_exists(XOOPS_ROOT_PATH . '/modules/userpage/language/' . $xoopsConfig['language'] . '/modinfo.php')) {
        require_once XOOPS_ROOT_PATH . '/modules/userpage/language/' . $xoopsConfig['language'] . '/modinfo.php';
    } else {
        require_once XOOPS_ROOT_PATH . '/modules/userpage/language/english/modinfo.php';
    }

    echo "<div id='buttontop'>";
    echo '<table style="width: 100%; padding: 0; " cellspacing="0"><tr>';
    echo '<td style="width: 60%; font-size: 10px; text-align: left; color: #2F5376; padding: 0 6px; line-height: 18px;"><a class="nobutton" href="../../system/admin.php?fct=preferences&amp;op=showmod&amp;mod='
         . $xoopsModule->getVar('mid')
         . '">'
         . _AM_USERPAGE_GENERALSET
         . '</a> | <a href="../index.php">'
         . _AM_USERPAGE_GOTOMOD
         . '</a></td>';
    echo '<td style="width: 40%; font-size: 10px; text-align: right; color: #2F5376; padding: 0 6px; line-height: 18px;"><b>' . $xoopsModule->name() . '  ' . _AM_USERPAGE_MODULEADMIN . '</b> ' . $breadcrumb . '</td>';
    echo '</tr></table>';
    echo '</div>';

    echo "<div id='buttonbar'>";
    echo '<ul>';
    echo "<li id='" . $tblColors[0] . "'><a href=\"index.php?op=stats\"\"><span>" . _MI_USERPAGE_ADMMENU1 . "</span></a></li>\n";
    echo '</ul></div>';
    echo '<br><br><pre>&nbsp;</pre><pre>&nbsp;</pre><br>';
}

/**
 * @param string $tablename
 * @param string $iconname
 */
function userpage_collapsableBar($tablename = '', $iconname = '')
{
    ?>
    <script type="text/javascript"><!--
        function goto_URL(object) {
            window.location.href = object.options[object.selectedIndex].value;
        }

        function toggle(id) {
            if (document.getElementById) {
                obj = document.getElementById(id);
            }
            if (document.all) {
                obj = document.all[id];
            }
            if (document.layers) {
                obj = document.layers[id];
            }
            if (obj) {
                if (obj.style.display == "none") {
                    obj.style.display = "";
                } else {
                    obj.style.display = "none";
                }
            }

            return false;
        }

        var iconClose = new Image();
        iconClose.src = '../assets/images/close12.gif';
        var iconOpen = new Image();
        iconOpen.src = '../assets/images/open12.gif';

        function toggleIcon(iconName) {
            if (document.images[iconName].src == window.iconOpen.src) {
                document.images[iconName].src = window.iconClose.src;
            }
            elseif(document.images[iconName].src == window.iconClose.src)
            {
                document.images[iconName].src = window.iconOpen.src;
            }

            return;
        }

        //-->
    </script>
    <?php
    echo "<h4 style=\"color: #2F5376; margin: 6px 0 0 0; \"><a href='#' onClick=\"toggle('" . $tablename . "'); toggleIcon('" . $iconname . "');\">";
}

?>
