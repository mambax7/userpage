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

defined('XOOPS_ROOT_PATH') || die('XOOPS root path not defined');

//function hex2dec
//returns an associative array (keys: R,G,B) from
//a hex html code (e.g. #3FE5AA)
/**
 * @param string $couleur
 * @return array
 */
function hex2dec($couleur = '#000000')
{
    $R                = mb_substr($couleur, 1, 2);
    $rouge            = hexdec($R);
    $V                = mb_substr($couleur, 3, 2);
    $vert             = hexdec($V);
    $B                = mb_substr($couleur, 5, 2);
    $bleu             = hexdec($B);
    $tbl_couleur      = [];
    $tbl_couleur['R'] = $rouge;
    $tbl_couleur['G'] = $vert;
    $tbl_couleur['B'] = $bleu;

    return $tbl_couleur;
}

//conversion pixel -> millimeter in 72 dpi
/**
 * @param $px
 * @return float|int
 */
function px2mm($px)
{
    return $px * 25.4 / 72;
}

/**
 * @param $html
 * @return string
 */
function txtentities($html)
{
    $trans = get_html_translation_table(HTML_ENTITIES);
    $trans = array_flip($trans);

    return strtr($html, $trans);
}

////////////////////////////////////

//class PDF extends FPDF

/**
 * Class PDF
 */
class PDF extends PDF_language
{
    //variables of html parser
    public $B;
    public $I;
    public $U;
    public $HREF;
    public $CENTER = '';
    public $ALIGN  = '';
    public $IMG;
    public $SRC;
    public $WIDTH;
    public $HEIGHT;
    public $fontList;
    public $issetfont;
    public $issetcolor;
    public $iminfo = [0, 0];

    /**
     * PDF constructor.
     * @param string $orientation
     * @param string $unit
     * @param string $format
     */
    public function __construct($orientation = 'P', $unit = 'mm', $format = 'A4')
    {
        //Call parent constructor
        $this->PDF_language($orientation, $unit, $format);
        //Initialization
        $this->B        = 0;
        $this->I        = 0;
        $this->U        = 0;
        $this->HREF     = '';
        $this->CENTER   = '';
        $this->ALIGN    = '';
        $this->IMG      = '';
        $this->SRC      = '';
        $this->WIDTH    = '';
        $this->HEIGHT   = '';
        $this->fontlist = ['arial', 'times', 'courier', 'helvetica', 'symbol'];

        $this->issetfont  = false;
        $this->issetcolor = false;
    }

    //////////////////////////////////////
    //html parser

    /**
     * @param $html
     * @param $scale
     */
    public function WriteHTML($html, $scale)
    {
        //    $html=strip_tags($html,"<b><u><i><a><img><p><br><strong><em><font><tr><blockquote>"); //remove all unsupported tags
        $html = str_replace("\n", ' ', $html); //replace carriage returns by spaces
        $a    = preg_split('/<(.*)>/U', $html, -1, PREG_SPLIT_DELIM_CAPTURE); //explodes the string
        foreach ($a as $i => $e) {
            if (0 == $i % 2) {
                //Text
                if ($this->HREF) {
                    $this->PutLink($this->HREF, $e);
                } elseif ($this->IMG) {
                    $this->PutImage($this->SRC, $scale);
                } elseif ($this->CENTER) {
                    $this->Cell(0, 5, $e, 0, 1, 'C');
                } elseif ('center' === $this->ALIGN) {
                    $this->Cell(0, 5, $e, 0, 1, 'C');
                } elseif ('right' === $this->ALIGN) {
                    $this->Cell(0, 5, $e, 0, 1, 'R');
                } elseif ('left' === $this->ALIGN) {
                    $this->Cell(0, 5, $e, 0, 1, 'L');
                } else {
                    $this->Write(5, stripslashes(txtentities($e)));
                }
            } else {
                //Tag
                if ('/' === $e[0]) {
                    $this->CloseTag(mb_strtoupper(mb_substr($e, 1)));
                } else {
                    //Extract attributes
                    $a2   = explode(' ', $e);
                    $tag  = mb_strtoupper(array_shift($a2));
                    $attr = [];
                    foreach ($a2 as $v) {
                        if (ereg('^([^=]*)=["\']?([^"\']*)["\']?$', $v, $a3)) {
                            $attr[mb_strtoupper($a3[1])] = $a3[2];
                        }
                    }
                    $this->OpenTag($tag, $attr, $scale);
                }
            }
        }
    }

    /**
     * @param $tag
     * @param $attr
     * @param $scale
     */
    public function OpenTag($tag, $attr, $scale)
    {
        //Opening tag
        switch ($tag) {
            case 'STRONG':
                $this->SetStyle('B', true);
                break;
            case 'EM':
                $this->SetStyle('I', true);
                break;
            case 'B':
            case 'I':
            case 'U':
                $this->SetStyle($tag, true);
                break;
            case 'A':
                $this->HREF = $attr['HREF'];
                break;
            case 'P':
                $this->ALIGN = $attr['ALIGN'];
                break;
            case 'IMG':
                $this->IMG    = $attr['IMG'];
                $this->SRC    = $attr['SRC'];
                $this->WIDTH  = $attr['WIDTH'];
                $this->HEIGHT = $attr['HEIGHT'];
                $this->PutImage($attr[SRC], $scale);
                break;
            case 'TR':
            case 'BLOCKQUOTE':
            case 'BR':
                $this->Ln(5);
                break;
            case 'HR':
                if ('' != $attr['WIDTH']) {
                    $Width = $attr['WIDTH'];
                } else {
                    $Width = $this->w - $this->lMargin - $this->rMargin;
                }
                $this->Ln(2);
                $x = $this->GetX();
                $y = $this->GetY();
                $this->SetLineWidth(0.4);
                $this->Line($x, $y, $x + $Width, $y);
                $this->SetLineWidth(0.2);
                $this->Ln(2);
                break;
            case 'FONT':
                if (isset($attr['COLOR']) and '' != $attr['COLOR']) {
                    $coul = hex2dec($attr['COLOR']);
                    $this->SetTextColor($coul['R'], $coul['G'], $coul['B']);
                    $this->issetcolor = true;
                }
                if (isset($attr['FACE']) and in_array(mb_strtolower($attr['FACE']), $this->fontlist)) {
                    $this->SetFont(mb_strtolower($attr['FACE']));
                    $this->issetfont = true;
                }
                break;
        }
    }

    /**
     * @param $tag
     */
    public function CloseTag($tag)
    {
        //Closing tag
        if ('STRONG' === $tag) {
            $tag = 'B';
        }
        if ('EM' === $tag) {
            $tag = 'I';
        }
        if ('B' === $tag or 'I' === $tag or 'U' === $tag) {
            $this->SetStyle($tag, false);
        }
        if ('A' === $tag) {
            $this->HREF = '';
        }
        if ('P' === $tag) {
            $this->ALIGN = '';
        }
        if ('IMG' === $tag) {
            $this->IMG    = '';
            $this->SRC    = '';
            $this->WIDTH  = '';
            $this->HEIGHT = '';
        }
        if ('FONT' === $tag) {
            if (true === $this->issetcolor) {
                $this->SetTextColor(0);
            }
            if ($this->issetfont) {
                $this->SetFont('arial');
                $this->issetfont = false;
            }
        }
    }

    /**
     * @param $tag
     * @param $enable
     */
    public function SetStyle($tag, $enable)
    {
        //Modify style and select corresponding font
        $this->$tag += ($enable ? 1 : -1);
        $style      = '';
        foreach (['B', 'I', 'U'] as $s) {
            if ($this->$s > 0) {
                $style .= $s;
            }
        }
        $this->SetFont('', $style);
    }

    /**
     * @param $URL
     * @param $txt
     */
    public function PutLink($URL, $txt)
    {
        //Put a hyperlink
        $this->SetTextColor(0, 0, 255);
        $this->SetStyle('U', true);
        $this->Write(5, $txt, $URL);
        $this->SetStyle('U', false);
        $this->SetTextColor(0);
    }

    //put the image in pdf with scaling...
    //width and height-options inside the IMG-Tag are ignored,
    //we get the image info directly from PHP...
    //$scale is the global scaling factor, passing through from WriteHTML()
    //(c)2004/03/12 by St@neCold
    /**
     * @param $url
     * @param $scale
     */
    public function PutImage($url, $scale)
    {
        if ($scale < 0) {
            $scale = 0;
        }
        //$scale<=0: put NO image inside the pdf!
        if ($scale > 0) {
            $xsflag = 0;
            $ysflag = 0;
            $yhflag = 0;
            $xscale = 1;
            $yscale = 1;
            //get image info
            $oposy  = $this->GetY();
            $url    = str_replace(XOOPS_URL, XOOPS_ROOT_PATH, $url);
            $iminfo = @getimagesize($url);
            $iw     = $scale * px2mm($iminfo[0]);
            $ih     = $scale * px2mm($iminfo[1]);
            $iw     = $iw ?: 1;
            $ih     = $ih ?: 1;
            $nw     = $iw;
            $nh     = $ih;
            //resizing in x-direction
            $xsflag = 0;
            if ($iw > 150) {
                $xscale = 150 / $iw;
                $yscale = $xscale;
                $nw     = $xscale * $iw;
                $nh     = $xscale * $ih;
                $xsflag = 1;
            }
            //now eventually resizing in y-direction
            $ysflag = 0;
            if (($oposy + $nh) > 250) {
                $yscale = (250 - $oposy) / $ih;
                $nw     = $yscale * $iw;
                $nh     = $yscale * $ih;
                $ysflag = 1;
            }
            //uups, if the scaling factor of resized image is < 0.33
            //remark: without(!) the global factor $scale!
            //that's hard -> on the next page please...
            $yhflag = 0;
            if ($yscale < 0.33 and (1 == $xsflag or 1 == $ysflag)) {
                $nw = $xscale * $iw;
                $nh = $xscale * $ih;
                0 == $ysflag;
                1 == $xsflag;
                $yhflag = 1;
            }
            if (1 == $yhflag) {
                $this->AddPage();
            }
            $oposy = $this->GetY();
            $this->Image($url, $this->GetX(), $this->GetY(), $nw, $nh);
            $this->SetY($oposy + $nh);
            if (0 == $yhflag and 1 == $ysflag) {
                $this->AddPage();
            }
        }
    }

    public function Footer()
    {
        //print footer
        //
        global $pdf_config;

        //date+time
        $printpdfdate = date($pdf_config['dateformat']);
        //Position and Font
        $this->SetXY(25, -25);
        $this->SetTextColor(0, 0, 255);
        $this->SetFont($pdf_config['font']['footer']['family'], $pdf_config['font']['footer']['style'], $pdf_config['font']['footer']['size']);
        //Link+Page number
        $this->Cell(0, 10, $pdf_config['url'], 'T', 0, 'L', 0, $pdf_config['url']);
        $pn  = $this->PageNo();
        $out = $printpdfdate;
        $out .= ' - ';
        $out .= $pn;
        $this->SetFont($pdf_config['font']['footer']['family'], $pdf_config['font']['footer']['style'], $pdf_config['font']['footer']['size']);
        $this->Cell(0, 10, $out, 'T', 0, 'R', 0, $pdf_config['mail']);
    }

    /**
     * @param $file
     * @return array|null
     */
    public function _parsegif($file)
    {
        require_once __DIR__ . '/gif.php'; //GIF class in pure PHP from Yamasoft (http://www.yamasoft.com/php-gif.zip)

        $h   = 0;
        $w   = 0;
        $gif = new CGIF();
        if (!$gif) {
            $this->Error("GIF parser: unable to open file $file");

            return null;
        }
        if (empty($gif->m_img->m_data)) {
            return null;
        }

        if ($gif->m_img->m_gih->m_bLocalClr) {
            $nColors = $gif->m_img->m_gih->m_nTableSize;
            $pal     = $gif->m_img->m_gih->m_colorTable->toString();
            if (-1 != $bgColor) {
                $bgColor = $gif->m_img->m_gih->m_colorTable->colorIndex($bgColor);
            }
            $colspace = 'Indexed';
        } elseif ($gif->m_gfh->m_bGlobalClr) {
            $nColors = $gif->m_gfh->m_nTableSize;
            $pal     = $gif->m_gfh->m_colorTable->toString();
            if (-1 != $bgColor) {
                $bgColor = $gif->m_gfh->m_colorTable->colorIndex($bgColor);
            }
            $colspace = 'Indexed';
        } else {
            $nColors  = 0;
            $bgColor  = -1;
            $colspace = 'DeviceGray';
            $pal      = '';
        }

        $trns = '';
        if ($gif->m_img->m_bTrans && ($nColors > 0)) {
            $trns = [$gif->m_img->m_nTrans];
        }

        $data = $gif->m_img->m_data;
        $w    = $gif->m_gfh->m_nWidth;
        $h    = $gif->m_gfh->m_nHeight;

        if ('Indexed' === $colspace and empty($pal)) {
            $this->Error('Missing palette in ' . $file);
        }

        if ($this->compress) {
            $data = gzcompress($data);

            return ['w' => $w, 'h' => $h, 'cs' => $colspace, 'bpc' => 8, 'f' => 'FlateDecode', 'pal' => $pal, 'trns' => $trns, 'data' => $data];
        }

        return ['w' => $w, 'h' => $h, 'cs' => $colspace, 'bpc' => 8, 'pal' => $pal, 'trns' => $trns, 'data' => $data];
    }
}
