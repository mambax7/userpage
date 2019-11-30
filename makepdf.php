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
error_reporting(0);
@$xoopsLogger->activated = false;

require_once XOOPS_ROOT_PATH . '/modules/userpage/fpdf/fpdf.inc.php';

/**
 * Internal function used for PDF
 * @param mixed $document
 * @return string|string[]|null
 */
function userpage_html2text($document)
{
    // PHP Manual:: function preg_replace
    // $document should contain an HTML document.
    // This will remove HTML tags, javascript sections
    // and white space. It will also convert some
    // common HTML entities to their text equivalent.

    $search = [
        "'<script[^>]*?>.*?</script>'si",  // Strip out javascript
        "'<[\/\!]*?[^<>]*?>'si",          // Strip out HTML tags
        "'([\r\n])[\s]+'",                // Strip out white space
        "'&(quot|#34);'i",                // Replace HTML entities
        "'&(amp|#38);'i",
        "'&(lt|#60);'i",
        "'&(gt|#62);'i",
        "'&(nbsp|#160);'i",
        "'&(iexcl|#161);'i",
        "'&(cent|#162);'i",
        "'&(pound|#163);'i",
        "'&(copy|#169);'i",
        "'&#(\d+);'e",
    ];                    // evaluate as php

    $replace = [
        '',
        '',
        '\\1',
        '"',
        '&',
        '<',
        '>',
        ' ',
        chr(161),
        chr(162),
        chr(163),
        chr(169),
        'chr(\\1)',
    ];

    $text = preg_replace($search, $replace, $document);

    return $text;
}

$myts = \MyTextSanitizer::getInstance();

$page_id = isset($_GET['page_id']) ? (int)$_GET['page_id'] : 0;

if (empty($page_id)) {
    redirect_header('index.php', 2, _ERRORS);
    exit();
}

$userpageHandler = \XoopsModules\Userpage\Helper::getInstance()->getHandler('Page');
$allowhtml        = Utility::getModuleOption('allowhtml');

$criteria = new \Criteria('up_pageid', $page_id, '=');
$cnt      = $userpageHandler->getCount($criteria);
if ($cnt > 0) {
    $pagetbl = $userpageHandler->getObjects($criteria);
    $page    = $pagetbl[0];
} else {    // Page not found
    redirect_header(XOOPS_URL . '/index.php', 2, _USERPAGE_PAGE_NOT_FOUND);
    exit();
}
$page->setVar('dohtml', $allowhtml);

$pdf_title       = $page->getVar('up_title', 'n');
$pdf_content     = $page->getVar('up_text', 'n');
$pdf_author      = $page->uname();
$pdf_topic_title = $page->getVar('up_title', 'n');
$pdf_title       = $page->getVar('up_title', 'n');
$pdf_subtitle    = '';
$pdf_subsubtitle = '';
$pdf_author      = $page->uname();
$pdf_date        = formatTimestamp($page->getVar('up_created'), Utility::getModuleOption('dateformat'));
$pdf_url         = $page->getURL();
// ***************************************************************************************************************************************

$pdf_topic_title          = userpage_html2text($myts->undoHtmlSpecialChars($pdf_topic_title));
$forumdata['topic_title'] = $pdf_topic_title;
$pdf_data['title']        = $pdf_title;
$pdf_data['subtitle']     = userpage_html2text($pdf_subtitle);
$pdf_data['subsubtitle']  = userpage_html2text($pdf_subsubtitle);
$pdf_data['date']         = $pdf_date;
$pdf_data['content']      = $myts->undoHtmlSpecialChars($pdf_content);
$pdf_data['author']       = $pdf_author;

//Other stuff
$puff   = '<br>';
$puffer = '<br><br><br>';

//create the A4-PDF...
$pdf_config['slogan']  = $xoopsConfig['sitename'] . ' - ' . $xoopsConfig['slogan'];
$pdf_config['creator'] = 'USERPAGE - Instant Zero';
$pdf_config['url']     = $pdf_url;

$pdf = new PDF();
if (method_exists($pdf, 'encoding')) {
    $pdf->encoding($pdf_data, _CHARSET);
}
$pdf->SetCreator($pdf_config['creator']);
$pdf->SetTitle($pdf_data['title']);
$pdf->SetAuthor($pdf_config['url']);
$pdf->SetSubject($pdf_data['author']);
$out = $pdf_config['url'] . ', ' . $pdf_data['author'] . ', ' . $pdf_data['title'] . ', ' . $pdf_data['subtitle'] . ', ' . $pdf_data['subsubtitle'];
$pdf->SetKeywords($out);
$pdf->SetAutoPageBreak(true, 25);
$pdf->SetMargins($pdf_config['margin']['left'], $pdf_config['margin']['top'], $pdf_config['margin']['right']);
$pdf->Open();

//First page
$pdf->AddPage();
$pdf->SetXY(24, 25);
$pdf->SetTextColor(10, 60, 160);
$pdf->SetFont($pdf_config['font']['slogan']['family'], $pdf_config['font']['slogan']['style'], $pdf_config['font']['slogan']['size']);
$pdf->WriteHTML($pdf_config['slogan'], $pdf_config['scale']);
$pdf->Line(25, 30, 190, 30);
$pdf->SetXY(25, 35);
$pdf->SetFont($pdf_config['font']['title']['family'], $pdf_config['font']['title']['style'], $pdf_config['font']['title']['size']);
$pdf->WriteHTML($pdf_data['title'], $pdf_config['scale']);

if ('' != $pdf_data['subtitle']) {
    $pdf->WriteHTML($puff, $pdf_config['scale']);
    $pdf->SetFont($pdf_config['font']['subtitle']['family'], $pdf_config['font']['subtitle']['style'], $pdf_config['font']['subtitle']['size']);
    $pdf->WriteHTML($pdf_data['subtitle'], $pdf_config['scale']);
}
if ('' != $pdf_data['subsubtitle']) {
    $pdf->WriteHTML($puff, $pdf_config['scale']);
    $pdf->SetFont($pdf_config['font']['subsubtitle']['family'], $pdf_config['font']['subsubtitle']['style'], $pdf_config['font']['subsubtitle']['size']);
    $pdf->WriteHTML($pdf_data['subsubtitle'], $pdf_config['scale']);
}

$pdf->WriteHTML($puff, $pdf_config['scale']);
$pdf->SetFont($pdf_config['font']['author']['family'], $pdf_config['font']['author']['style'], $pdf_config['font']['author']['size']);
$out = USERPAGE_PDF_AUTHOR . ': ';
$out .= $pdf_data['author'];
$pdf->WriteHTML($out, $pdf_config['scale']);
$pdf->WriteHTML($puff, $pdf_config['scale']);
$out = USERPAGE_PDF_DATE;
$out .= $pdf_data['date'];
$pdf->WriteHTML($out, $pdf_config['scale']);
$pdf->WriteHTML($puff, $pdf_config['scale']);

$pdf->SetTextColor(0, 0, 0);
$pdf->WriteHTML($puffer, $pdf_config['scale']);

$pdf->SetFont($pdf_config['font']['content']['family'], $pdf_config['font']['content']['style'], $pdf_config['font']['content']['size']);
$pdf->WriteHTML($pdf_data['content'], $pdf_config['scale']);
$pdf->Output();
