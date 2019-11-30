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

/**
 * A set of useful and common functions
 *
 * @package       userpage
 * @author        Hervé Thouzard - Instant Zero (http://xoops.instant-zero.com)
 * @copyright (c) Instant Zero
 *
 * Note: You should be able to use it without the need to instanciate it.
 */
defined('XOOPS_ROOT_PATH') || die('XOOPS root path not defined');

/**
 * Class userpage_utils
 */
class userpage_utils
{
    const MODULE_NAME = 'userpage';

    /**
     * Access the only instance of this class
     *
     * @return object
     *
     * @static
     * @staticvar   object
     */
    public static function getInstance()
    {
        static $instance;
        if (null === $instance) {
            $instance = new static();
        }

        return $instance;
    }

    /**
     * Returns a module's option (with cache)
     *
     * @param string $option    module option's name
     * @param bool   $withCache Do we have to use some cache ?
     * @return mixed   option's value
     */
    public function getModuleOption($option, $withCache = true)
    {
        global $xoopsModuleConfig, $xoopsModule;
        $repmodule = self::MODULE_NAME;
        static $options = [];
        if (is_array($options) && array_key_exists($option, $options) && $withCache) {
            return $options[$option];
        }

        $retval = false;
        if (isset($xoopsModuleConfig) && (is_object($xoopsModule) && $xoopsModule->getVar('dirname') == $repmodule && $xoopsModule->getVar('isactive'))) {
            if (isset($xoopsModuleConfig[$option])) {
                $retval = $xoopsModuleConfig[$option];
            }
        } else {
            $moduleHandler = xoops_getHandler('module');
            $module         =  $moduleHandler->getByDirname($repmodule);
            $configHandler = xoops_getHandler('config');
            if ($module) {
                $moduleConfig = &$configHandler->getConfigsByCat(0, $module->getVar('mid'));
                if (isset($moduleConfig[$option])) {
                    $retval = $moduleConfig[$option];
                }
            }
        }
        $options[$option] = $retval;

        return $retval;
    }

    /**
     * Is Xoops 2.3.x ?
     *
     * @return bool need to say it ?
     */
    public function isX23()
    {
        $x23 = false;
        $xv  = str_replace('XOOPS ', '', XOOPS_VERSION);
        if ((int)mb_substr($xv, 2, 1) >= 3) {
            $x23 = true;
        }

        return $x23;
    }

    /**
     * Retreive an editor according to the module's option "form_options"
     *
     * @param string $caption Caption to give to the editor
     * @param string $name    Editor's name
     * @param string $value   Editor's value
     * @param string $width   Editor's width
     * @param string $height  Editor's height
     * @param mixed  $supplemental
     * @return object The editor to use
     */
    public function &getWysiwygForm($caption, $name, $value = '', $width = '100%', $height = '400px', $supplemental = '')
    {
        $editor                   = false;
        $editor_configs           = [];
        $editor_configs['name']   = $name;
        $editor_configs['value']  = $value;
        $editor_configs['rows']   = 35;
        $editor_configs['cols']   = 60;
        $editor_configs['width']  = '100%';
        $editor_configs['height'] = '400px';

        $editor_option = mb_strtolower(self::getModuleOption('usekiovi'));

        if (self::isX23()) {
            $editor = new \XoopsFormEditor($caption, $editor_option, $editor_configs);

            return $editor;
        }

        // Only for Xoops 2.0.x
        switch ($editor_option) {
            case 'fckeditor':
                if (is_readable(XOOPS_ROOT_PATH . '/class/fckeditor/formfckeditor.php')) {
                    require_once XOOPS_ROOT_PATH . '/class/fckeditor/formfckeditor.php';
                    $editor = new \XoopsFormFckeditor($caption, $name, $value);
                }
                break;
            case 'htmlarea':
                if (is_readable(XOOPS_ROOT_PATH . '/class/htmlarea/formhtmlarea.php')) {
                    require_once XOOPS_ROOT_PATH . '/class/htmlarea/formhtmlarea.php';
                    $editor = new \XoopsFormHtmlarea($caption, $name, $value);
                }
                break;
            case 'dhtmltextarea':
                $editor = new \XoopsFormDhtmlTextArea($caption, $name, $value, 10, 50, $supplemental);
                break;
            case 'textarea':
                $editor = new \XoopsFormTextArea($caption, $name, $value);
                break;
            case 'tinyeditor':
            case 'tinymce':
                if (is_readable(XOOPS_ROOT_PATH . '/class/xoopseditor/tinyeditor/formtinyeditortextarea.php')) {
                    require_once XOOPS_ROOT_PATH . '/class/xoopseditor/tinyeditor/formtinyeditortextarea.php';
                    $editor = new \XoopsFormTinyeditorTextArea(['caption' => $caption, 'name' => $name, 'value' => $value, 'width' => '100%', 'height' => '400px']);
                }
                break;
            case 'koivi':
                if (is_readable(XOOPS_ROOT_PATH . '/class/wysiwyg/formwysiwygtextarea.php')) {
                    require_once XOOPS_ROOT_PATH . '/class/wysiwyg/formwysiwygtextarea.php';
                    $editor = new \XoopsFormWysiwygTextArea($caption, $name, $value, $width, $height, '');
                }
                break;
        }

        return $editor;
    }

    /**
     * Create (in a link) a javascript confirmation's box
     *
     * @param string $message Message to display
     * @param bool   $form    Is this a confirmation for a form ?
     * @return string  the javascript code to insert in the link (or in the form)
     */
    public function javascriptLinkConfirm($message, $form = false)
    {
        if (!$form) {
            return "onclick=\"javascript:return confirm('" . str_replace("'", ' ', $message) . "')\"";
        }

        return "onSubmit=\"javascript:return confirm('" . str_replace("'", ' ', $message) . "')\"";
    }

    /**
     * Get current user IP
     *
     * @return string IP address (format Ipv4)
     */
    public function IP()
    {
        $proxy_ip = '';
        if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $proxy_ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED'])) {
            $proxy_ip = $_SERVER['HTTP_X_FORWARDED'];
        } elseif (!empty($_SERVER['HTTP_FORWARDED_FOR'])) {
            $proxy_ip = $_SERVER['HTTP_FORWARDED_FOR'];
        } elseif (!empty($_SERVER['HTTP_FORWARDED'])) {
            $proxy_ip = $_SERVER['HTTP_FORWARDED'];
        } elseif (!empty($_SERVER['HTTP_VIA'])) {
            $proxy_ip = $_SERVER['HTTP_VIA'];
        } elseif (!empty($_SERVER['HTTP_X_COMING_FROM'])) {
            $proxy_ip = $_SERVER['HTTP_X_COMING_FROM'];
        } elseif (!empty($_SERVER['HTTP_COMING_FROM'])) {
            $proxy_ip = $_SERVER['HTTP_COMING_FROM'];
        }
        $regs = [];
        if (!empty($proxy_ip) && $is_ip = ereg('^([0-9]{1,3}\.){3,3}[0-9]{1,3}', $proxy_ip, $regs) && count($regs) > 0) {
            $the_IP = $regs[0];
        } else {
            $the_IP = $_SERVER['REMOTE_ADDR'];
        }

        return $the_IP;
    }

    /**
     * Set the page's title, meta description and meta keywords
     * Datas are supposed to be sanitized
     *
     * @param string $pageTitle       Page's Title
     * @param string $metaDescription Page's meta description
     * @param string $metaKeywords    Page's meta keywords
     */
    public function setMetas($pageTitle = '', $metaDescription = '', $metaKeywords = '')
    {
        global $xoTheme, $xoTheme, $xoopsTpl;
        $xoopsTpl->assign('xoops_pagetitle', $pageTitle);
        if (isset($xoTheme) && is_object($xoTheme)) {
            if (!empty($metaKeywords)) {
                $xoTheme->addMeta('meta', 'keywords', $metaKeywords);
            }
            if (!empty($metaDescription)) {
                $xoTheme->addMeta('meta', 'description', $metaDescription);
            }
        } elseif (isset($xoopsTpl) && is_object($xoopsTpl)) {    // Compatibility for old Xoops versions
            if (!empty($metaKeywords)) {
                $xoopsTpl->assign('xoops_meta_keywords', $metaKeywords);
            }
            if (!empty($metaDescription)) {
                $xoopsTpl->assign('xoops_meta_description', $metaDescription);
            }
        }
    }

    /**
     * Remove module's cache
     */
    public function updateCache()
    {
        global $xoopsModule;
        $folder  = $xoopsModule->getVar('dirname');
        $tpllist = [];
        require_once XOOPS_ROOT_PATH . '/class/xoopsblock.php';
        require_once XOOPS_ROOT_PATH . '/class/template.php';
        $tplfileHandler = xoops_getHandler('tplfile');
        $tpllist         = $tplfileHandler->find(null, null, null, $folder);
        xoops_template_clear_module_cache($xoopsModule->getVar('mid'));            // Clear module's blocks cache

        foreach ($tpllist as $onetemplate) {    // Remove cache for each page.
            if ('module' === $onetemplate->getVar('tpl_type')) {
                //  Note, I've been testing all the other methods (like the one of Smarty) and none of them run, that's why I have used this code
                $files_del = [];
                $files_del = glob(XOOPS_CACHE_PATH . '/*' . $onetemplate->getVar('tpl_file') . '*');
                if (count($files_del) > 0 && is_array($files_del)) {
                    foreach ($files_del as $one_file) {
                        if (is_file($one_file)) {
                            unlink($one_file);
                        }
                    }
                }
            }
        }
    }

    /**
     * Redirect user with a message
     *
     * @param string $message message to display
     * @param string $url     The place where to go
     * @param mixed  $time
     */
    public function redirect($message = '', $url = 'index.php', $time = 2)
    {
        redirect_header($url, $time, $message);
        exit();
    }

    /**
     * Internal function used to get the handler of the current module
     *
     * @return object The module
     */
    protected function _getModule()
    {
        static $mymodule;
        if (!isset($mymodule)) {
            global $xoopsModule;
            if (isset($xoopsModule) && is_object($xoopsModule) && USERPAGE_DIRNAME == $xoopsModule->getVar('dirname')) {
                $mymodule = &$xoopsModule;
            } else {
                $hModule  = xoops_getHandler('module');
                $mymodule = $hModule->getByDirname(USERPAGE_DIRNAME);
            }
        }

        return $mymodule;
    }

    /**
     * Returns the module's name (as defined by the user in the module manager) with cache
     * @return string Module's name
     */
    public function getModuleName()
    {
        static $moduleName;
        if (!isset($moduleName)) {
            $mymodule   = self::_getModule();
            $moduleName = $mymodule->getVar('name');
        }

        return $moduleName;
    }

    /**
     * Create a title for the href tags inside html links
     *
     * @param string $title Text to use
     * @return string Formated text
     */
    public function makeHrefTitle($title)
    {
        $s = "\"'";
        $r = '  ';

        return strtr($title, $s, $r);
    }

    /**
     * V�rifie que l'utilisateur courant fait partie du groupe des administrateurs
     *
     * @return bool Admin or not
     */
    public function isAdmin()
    {
        global $xoopsUser, $xoopsModule;
        if (is_object($xoopsUser)) {
            if (in_array(XOOPS_GROUP_ADMIN, $xoopsUser->getGroups())) {
                return true;
            }
            if (isset($xoopsModule)) {
                if ($xoopsUser->isAdmin($xoopsModule->getVar('mid'))) {
                    return true;
                }

                return false;
            }

            return false;
        }

        return false;
    }

    /**
     * This function indicates if the current Xoops version needs to add asterisks to required fields in forms
     *
     * @return bool Yes = we need to add them, false = no
     */
    public function needsAsterisk()
    {
        if (self::isX23()) {
            return false;
        }
        if (false !== mb_strpos(mb_strtolower(XOOPS_VERSION), 'impresscms')) {
            return false;
        }
        if (false === mb_strpos(mb_strtolower(XOOPS_VERSION), 'legacy')) {
            $xv = xoops_trim(str_replace('XOOPS ', '', XOOPS_VERSION));
            if ((int)mb_substr($xv, 4, 2) >= 17) {
                return false;
            }
        }

        return true;
    }

    /**
     * Mark the mandatory fields of a form with a star
     *
     * @param object $sform The form to modify
     * @return object The modified form
     */
    public function &formMarkRequiredFields(&$sform)
    {
        if (self::needsAsterisk()) {
            $required = [];
            foreach ($sform->getRequired() as $item) {
                $required[] = $item->_name;
            }
            $elements = [];
            $elements = &$sform->getElements();
            $cnt      = count($elements);
            for ($i = 0; $i < $cnt; ++$i) {
                if (is_object($elements[$i]) && in_array($elements[$i]->_name, $required)) {
                    $elements[$i]->_caption .= ' *';
                }
            }
        }

        return $sform;
    }

    /**
     * Create an html heading (from h1 to h6)
     *
     * @param string $title The text to use
     * @param int    $level Level to return
     * @return string  The heading
     */
    public function htitle($title = '', $level = 1)
    {
        printf('<h%01d>%s</h%01d>', $level, $title, $level);
    }

    /**
     * Replace html entities with their ASCII equivalent
     *
     * @param string $chaine The string undecode
     * @return string The undecoded string
     */
    public function unhtml($chaine)
    {
        $search = $replace = [];
        $chaine = html_entity_decode($chaine);

        for ($i = 0; $i <= 255; ++$i) {
            $search[]  = '&#' . $i . ';';
            $replace[] = chr($i);
        }
        $replace[] = '...';
        $search[]  = '�';
        $replace[] = "'";
        $search[]  = '�';
        $replace[] = "'";
        $search[]  = '�';
        $replace[] = '-';
        $search[]  = '&bull;';    // $replace[] = '�';
        $replace[] = '�';
        $search[]  = '&mdash;';
        $replace[] = '-';
        $search[]  = '&ndash;';
        $replace[] = '-';
        $search[]  = '&shy;';
        $replace[] = '"';
        $search[]  = '&quot;';
        $replace[] = '&';
        $search[]  = '&amp;';
        $replace[] = '�';
        $search[]  = '&circ;';
        $replace[] = '�';
        $search[]  = '&iexcl;';
        $replace[] = '�';
        $search[]  = '&brvbar;';
        $replace[] = '�';
        $search[]  = '&uml;';
        $replace[] = '�';
        $search[]  = '&macr;';
        $replace[] = '�';
        $search[]  = '&acute;';
        $replace[] = '�';
        $search[]  = '&cedil;';
        $replace[] = '�';
        $search[]  = '&iquest;';
        $replace[] = '�';
        $search[]  = '&tilde;';
        $replace[] = "'";
        $search[]  = '&lsquo;';    // $replace[]='�';
        $replace[] = "'";
        $search[]  = '&rsquo;';    // $replace[]='�';
        $replace[] = '�';
        $search[]  = '&sbquo;';
        $replace[] = "'";
        $search[]  = '&ldquo;';    // $replace[]='�';
        $replace[] = "'";
        $search[]  = '&rdquo;';    // $replace[]='�';
        $replace[] = '�';
        $search[]  = '&bdquo;';
        $replace[] = '�';
        $search[]  = '&lsaquo;';
        $replace[] = '�';
        $search[]  = '&rsaquo;';
        $replace[] = '<';
        $search[]  = '&lt;';
        $replace[] = '>';
        $search[]  = '&gt;';
        $replace[] = '�';
        $search[]  = '&plusmn;';
        $replace[] = '�';
        $search[]  = '&laquo;';
        $replace[] = '�';
        $search[]  = '&raquo;';
        $replace[] = '�';
        $search[]  = '&times;';
        $replace[] = '�';
        $search[]  = '&divide;';
        $replace[] = '�';
        $search[]  = '&cent;';
        $replace[] = '�';
        $search[]  = '&pound;';
        $replace[] = '�';
        $search[]  = '&curren;';
        $replace[] = '�';
        $search[]  = '&yen;';
        $replace[] = '�';
        $search[]  = '&sect;';
        $replace[] = '�';
        $search[]  = '&copy;';
        $replace[] = '�';
        $search[]  = '&not;';
        $replace[] = '�';
        $search[]  = '&reg;';
        $replace[] = '�';
        $search[]  = '&deg;';
        $replace[] = '�';
        $search[]  = '&micro;';
        $replace[] = '�';
        $search[]  = '&para;';
        $replace[] = '�';
        $search[]  = '&middot;';
        $replace[] = '�';
        $search[]  = '&dagger;';
        $replace[] = '�';
        $search[]  = '&Dagger;';
        $replace[] = '�';
        $search[]  = '&permil;';
        $replace[] = 'Euro';
        $search[]  = '&euro;';        // $replace[]='�'
        $replace[] = '�';
        $search[]  = '&frac14;';
        $replace[] = '�';
        $search[]  = '&frac12;';
        $replace[] = '�';
        $search[]  = '&frac34;';
        $replace[] = '�';
        $search[]  = '&sup1;';
        $replace[] = '�';
        $search[]  = '&sup2;';
        $replace[] = '�';
        $search[]  = '&sup3;';
        $replace[] = '�';
        $search[]  = '&aacute;';
        $replace[] = '�';
        $search[]  = '&Aacute;';
        $replace[] = '�';
        $search[]  = '&acirc;';
        $replace[] = '�';
        $search[]  = '&Acirc;';
        $replace[] = '�';
        $search[]  = '&agrave;';
        $replace[] = '�';
        $search[]  = '&Agrave;';
        $replace[] = '�';
        $search[]  = '&aring;';
        $replace[] = '�';
        $search[]  = '&Aring;';
        $replace[] = '�';
        $search[]  = '&atilde;';
        $replace[] = '�';
        $search[]  = '&Atilde;';
        $replace[] = '�';
        $search[]  = '&auml;';
        $replace[] = '�';
        $search[]  = '&Auml;';
        $replace[] = '�';
        $search[]  = '&ordf;';
        $replace[] = '�';
        $search[]  = '&aelig;';
        $replace[] = '�';
        $search[]  = '&AElig;';
        $replace[] = '�';
        $search[]  = '&ccedil;';
        $replace[] = '�';
        $search[]  = '&Ccedil;';
        $replace[] = '�';
        $search[]  = '&eth;';
        $replace[] = '�';
        $search[]  = '&ETH;';
        $replace[] = '�';
        $search[]  = '&eacute;';
        $replace[] = '�';
        $search[]  = '&Eacute;';
        $replace[] = '�';
        $search[]  = '&ecirc;';
        $replace[] = '�';
        $search[]  = '&Ecirc;';
        $replace[] = '�';
        $search[]  = '&egrave;';
        $replace[] = '�';
        $search[]  = '&Egrave;';
        $replace[] = '�';
        $search[]  = '&euml;';
        $replace[] = '�';
        $search[]  = '&Euml;';
        $replace[] = '�';
        $search[]  = '&fnof;';
        $replace[] = '�';
        $search[]  = '&iacute;';
        $replace[] = '�';
        $search[]  = '&Iacute;';
        $replace[] = '�';
        $search[]  = '&icirc;';
        $replace[] = '�';
        $search[]  = '&Icirc;';
        $replace[] = '�';
        $search[]  = '&igrave;';
        $replace[] = '�';
        $search[]  = '&Igrave;';
        $replace[] = '�';
        $search[]  = '&iuml;';
        $replace[] = '�';
        $search[]  = '&Iuml;';
        $replace[] = '�';
        $search[]  = '&ntilde;';
        $replace[] = '�';
        $search[]  = '&Ntilde;';
        $replace[] = '�';
        $search[]  = '&oacute;';
        $replace[] = '�';
        $search[]  = '&Oacute;';
        $replace[] = '�';
        $search[]  = '&ocirc;';
        $replace[] = '�';
        $search[]  = '&Ocirc;';
        $replace[] = '�';
        $search[]  = '&ograve;';
        $replace[] = '�';
        $search[]  = '&Ograve;';
        $replace[] = '�';
        $search[]  = '&ordm;';
        $replace[] = '�';
        $search[]  = '&oslash;';
        $replace[] = '�';
        $search[]  = '&Oslash;';
        $replace[] = '�';
        $search[]  = '&otilde;';
        $replace[] = '�';
        $search[]  = '&Otilde;';
        $replace[] = '�';
        $search[]  = '&ouml;';
        $replace[] = '�';
        $search[]  = '&Ouml;';
        $replace[] = '�';
        $search[]  = '&oelig;';
        $replace[] = '�';
        $search[]  = '&OElig;';
        $replace[] = '�';
        $search[]  = '&scaron;';
        $replace[] = '�';
        $search[]  = '&Scaron;';
        $replace[] = '�';
        $search[]  = '&szlig;';
        $replace[] = '�';
        $search[]  = '&thorn;';
        $replace[] = '�';
        $search[]  = '&THORN;';
        $replace[] = '�';
        $search[]  = '&uacute;';
        $replace[] = '�';
        $search[]  = '&Uacute;';
        $replace[] = '�';
        $search[]  = '&ucirc;';
        $replace[] = '�';
        $search[]  = '&Ucirc;';
        $replace[] = '�';
        $search[]  = '&ugrave;';
        $replace[] = '�';
        $search[]  = '&Ugrave;';
        $replace[] = '�';
        $search[]  = '&uuml;';
        $replace[] = '�';
        $search[]  = '&Uuml;';
        $replace[] = '�';
        $search[]  = '&yacute;';
        $replace[] = '�';
        $search[]  = '&Yacute;';
        $replace[] = '�';
        $search[]  = '&yuml;';
        $replace[] = '�';
        $search[]  = '&Yuml;';
        $chaine    = str_replace($search, $replace, $chaine);

        return $chaine;
    }

    /**
     * Cr�ation d'une titre pour �tre utilis� par l'url rewriting
     *
     * @param string $content   Le texte � utiliser pour cr�er l'url
     * @param int    $urw       La limite basse pour cr�er les mots
     * @return string  Le texte � utiliser pour l'url
     *                          Note, some parts are from Solo's code
     */
    public function makeSeoUrl($content, $urw = 1)
    {
        $s       = "������������������������ܟ���������������������������� '()";
        $r       = 'AAAAAAOOOOOOEEEECIIIIUUUUYNaaaaaaooooooeeeeciiiiuuuuyn----';
        $content = self::unhtml($content);    // First, remove html entities
        $content = strtr($content, $s, $r);
        $content = strip_tags($content);
        $content = mb_strtolower($content);
        $content = htmlentities($content);    // TODO: V�rifier
        $content = preg_replace('/&([a-zA-Z])(uml|acute|grave|circ|tilde);/', '$1', $content);
        $content = html_entity_decode($content);
        $content = eregi_replace('quot', ' ', $content);
        $content = eregi_replace("'", ' ', $content);
        $content = eregi_replace('-', ' ', $content);
        $content = eregi_replace('[[:punct:]]', '', $content);
        // Selon option mais attention au fichier .htaccess !
        // $content = eregi_replace('[[:digit:]]','', $content);
        $content = preg_replace('/[^a-z|A-Z|0-9]/', '-', $content);

        $words    = explode(' ', $content);
        $keywords = '';
        foreach ($words as $word) {
            if (mb_strlen($word) >= $urw) {
                $keywords .= '-' . trim($word);
            }
        }
        if (!$keywords) {
            $keywords = '-';
        }
        // Supprime les tirets en double
        $keywords = str_replace('---', '-', $keywords);
        $keywords = str_replace('--', '-', $keywords);
        // Supprime un �ventuel tiret � la fin de la chaine
        if ('-' == mb_substr($keywords, mb_strlen($keywords) - 1, 1)) {
            $keywords = mb_substr($keywords, 0, mb_strlen($keywords) - 1);
        }

        return $keywords;
    }

    /**
     * Create the meta keywords based on the content
     *
     * @param string $content Content from which we have to create metakeywords
     * @return string The list of meta keywords
     */
    public function createMetaKeywords($content)
    {
        $keywordscount = 40;
        $keywordsorder = 0;

        $tmp = [];
        // Search for the "Minimum keyword length"
        if (isset($_SESSION['userpage_keywords_limit'])) {
            $limit = $_SESSION['userpage_keywords_limit'];
        } else {
            $configHandler                      = xoops_getHandler('config');
            $xoopsConfigSearch                   = &$configHandler->getConfigsByCat(XOOPS_CONF_SEARCH);
            $limit                               = $xoopsConfigSearch['keyword_min'];
            $_SESSION['userpage_keywords_limit'] = $limit;
        }
        $myts            = \MyTextSanitizer::getInstance();
        $content         = str_replace('<br>', ' ', $content);
        $content         = $myts->undoHtmlSpecialChars($content);
        $content         = strip_tags($content);
        $content         = mb_strtolower($content);
        $search_pattern  = ['&nbsp;', "\t", "\r\n", "\r", "\n", ',', '.', "'", ';', ':', ')', '(', '"', '?', '!', '{', '}', '[', ']', '<', '>', '/', '+', '-', '_', '\\', '*'];
        $replace_pattern = [' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''];
        $content         = str_replace($search_pattern, $replace_pattern, $content);
        $keywords        = explode(' ', $content);
        switch ($keywordsorder) {
            case 0:    // Ordre d'apparition dans le texte
                $keywords = array_unique($keywords);
                break;
            case 1:    // Ordre de fr�quence des mots
                $keywords = array_count_values($keywords);
                asort($keywords);
                $keywords = array_keys($keywords);
                break;
            case 2:    // Ordre inverse de la fr�quence des mots
                $keywords = array_count_values($keywords);
                arsort($keywords);
                $keywords = array_keys($keywords);
                break;
        }
        // Remove black listed words
        if ('' != xoops_trim(self::getModuleOption('metagen_blacklist'))) {
            $metagen_blacklist = str_replace("\r", '', self::getModuleOption('metagen_blacklist'));
            $metablack         = explode("\n", $metagen_blacklist);
            array_walk($metablack, 'trim');
            $keywords = array_diff($keywords, $metablack);
        }

        foreach ($keywords as $keyword) {
            if (mb_strlen($keyword) >= $limit && !is_numeric($keyword)) {
                $tmp[] = $keyword;
            }
        }
        $tmp = array_slice($tmp, 0, $keywordscount);
        if (count($tmp) > 0) {
            return implode(',', $tmp);
        }
        if (!isset($configHandler) || !is_object($configHandler)) {
            $configHandler = xoops_getHandler('config');
        }
        $xoopsConfigMetaFooter = &$configHandler->getConfigsByCat(XOOPS_CONF_METAFOOTER);
        if (isset($xoopsConfigMetaFooter['meta_keywords'])) {
            return $xoopsConfigMetaFooter['meta_keywords'];
        }

        return '';
    }

    /**
     * Create an infotip
     * @param mixed $text
     * @return string
     */
    public function makeInfotips($text)
    {
        $ret      = '';
        $infotips = self::getModuleOption('infotips');
        if ($infotips > 0) {
            $myts = \MyTextSanitizer::getInstance();
            $ret  = $myts->htmlSpecialChars(xoops_substr(strip_tags($text), 0, $infotips));
        }

        return $ret;
    }
}
