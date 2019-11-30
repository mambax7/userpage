<?php
// $Id: english.php,v 1.2 2005/04/18 01:22:29 phppp Exp $
//  ------------------------------------------------------------------------ //
//                XOOPS - PHP Content Management System                      //
//                    Copyright (c) 2000 XOOPS.org                           //
//                       <https://xoops.org>                             //
//  ------------------------------------------------------------------------ //
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
// Author: Kazumi Ono (AKA onokazu)                                          //
// URL: http://www.myweb.ne.jp/, https://xoops.org/, http://jp.xoops.org/ //
// Project: The XOOPS Project                                                //
// ------------------------------------------------------------------------- //

// For end users
defined('XOOPS_ROOT_PATH') || die('XOOPS root path not defined');

$valid_pfd_charset = 'ISO-8859-1';

$pdf_config['margin'] = [
    'left'  => 25,
    'top'   => 25,
    'right' => 25,
];

$pdf_config['logo'] = [
    'path'   => 'images/xoopsbb_slogo.png',
    'left'   => 150,
    'top'    => 5,
    'width'  => 0,
    'height' => 0,
];

$pdf_config['font']['slogan'] = [
    'family' => 'Arial',
    'style'  => 'bi',
    'size'   => 8,
];

$pdf_config['font']['title'] = [
    'family' => 'Arial',
    'style'  => 'biu',
    'size'   => 12,
];

$pdf_config['font']['subject'] = [
    'family' => 'Arial',
    'style'  => 'b',
    'size'   => 11,
];

$pdf_config['font']['author'] = [
    'family' => 'Arial',
    'style'  => '',
    'size'   => 10,
];

$pdf_config['font']['subtitle'] = [
    'family' => 'Arial',
    'style'  => 'b',
    'size'   => 11,
];

$pdf_config['font']['subsubtitle'] = [
    'family' => 'Arial',
    'style'  => 'b',
    'size'   => 10,
];

$pdf_config['font']['content'] = [
    'family' => 'Arial',
    'style'  => '',
    'size'   => 10,
];

$pdf_config['font']['footer'] = [
    'family' => 'Arial',
    'style'  => '',
    'size'   => 8,
];

if (!isset($myts)) {
    $myts = \MyTextSanitizer::getInstance();
}

$pdf_config['action_on_error'] = 0; // 0 - continue; 1 - die
$pdf_config['creator']         = 'USERPAGE BASED ON FPDF v1.53';
$pdf_config['url']             = XOOPS_URL;
$pdf_config['mail']            = 'mailto:' . $xoopsConfig['adminmail'];
$pdf_config['slogan']          = xoops_substr($myts->htmlSpecialChars($xoopsConfig['sitename']), 0, 30);
$pdf_config['scale']           = '0.8';
$pdf_config['dateformat']      = _DATESTRING;
$pdf_config['footerpage']      = _USERPAGE_PDF_PAGE;

// For local support sites
define('USERPAGE_PDF_FORUM', 'Forum');
define('USERPAGE_PDF_TOPIC', 'Topic');
define('USERPAGE_PDF_SUBJECT', 'Subject');
define('USERPAGE_PDF_AUTHOR', _POSTEDBY);
define('USERPAGE_PDF_DATE', _USERPAGE_POSTEDON);

// Usually you do not need change the following class if you are not using: S/T Chinese, Korean, Japanese
// For more details, refer to: http://fpdf.org

/**
 * Class PDF_language
 */
class PDF_language extends FPDF
{
    /**
     * PDF_language constructor.
     * @param string $orientation
     * @param string $unit
     * @param string $format
     */
    public function __construct($orientation = 'P', $unit = 'mm', $format = 'A4')
    {
        //Call parent constructor
        fpdf::__construct($orientation, $unit, $format);
    }

    /**
     * @param $msg
     */
    public function Error($msg)
    {
        global $pdf_config;
        if ($pdf_config['action_on_error']) {
            //Fatal error
            die('<B>FPDF error: </B>' . $msg);
        }
    }

    /**
     * @param $text
     * @param $in_charset
     */
    public function encoding(&$text, $in_charset)
    {
        $out_charset = $GLOBALS['valid_pfd_charset'];
        if (empty($in_charset) || empty($out_charset) || !strcasecmp($out_charset, $in_charset)) {
            return;
        }

        if (is_array($text) && count($text) > 0) {
            foreach ($text as $key => $val) {
                $this->_encoding($text[$key], $in_charset, $out_charset);
            }
        } else {
            $this->_encoding($text, $in_charset, $out_charset);
        }
    }

    /**
     * @param $text
     * @param $in_charset
     * @param $out_charset
     */
    public function _encoding(&$text, $in_charset, $out_charset)
    {
        // some conversion goes here
        // refer to schinese.php for example
    }
}
