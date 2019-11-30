<?php

namespace XoopsModules\Userpage;

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
require_once XOOPS_ROOT_PATH . '/kernel/object.php';
require_once XOOPS_ROOT_PATH . '/modules/userpage/include/common.php';

/**
 * Class Page
 * @package XoopsModules\Userpage
 */
class Page extends \XoopsObject
{
    /**
     * Page constructor.
     * @param null $id
     */
    public function __construct($id = null)
    {
        $this->db = \XoopsDatabaseFactory::getDatabaseConnection();
        $this->initVar('up_pageid', XOBJ_DTYPE_INT, null, false, 10);
        $this->initVar('up_uid', XOBJ_DTYPE_INT, null, false, 10);
        $this->initVar('up_title', XOBJ_DTYPE_TXTBOX, null, false, 255);
        $this->initVar('up_text', XOBJ_DTYPE_TXTAREA, null, false);
        $this->initVar('up_created', XOBJ_DTYPE_INT, null, false, 10);
        $this->initVar('up_hits', XOBJ_DTYPE_INT, null, false, 10);
        $this->initVar('dohtml', XOBJ_DTYPE_INT, 0, false);
        if (!empty($id)) {
            if (is_array($id)) {
                $this->assignVars($id);
            } else {
                $this->load((int)$id);
            }
        } else {
            $this->setNew();
        }
    }

    /**
     * @param $id
     */
    public function load($id)
    {
        $sql   = 'SELECT * FROM ' . $this->db->prefix('userpage') . ' WHERE up_pageid=' . (int)$id;
        $myrow = $this->db->fetchArray($this->db->query($sql));
        $this->assignVars($myrow);
        if (!$myrow) {
            $this->setNew();
        }
    }

    /**
     * Returns the user name for the current page (if the parameter is null)
     * @param mixed $uid
     * @return mixed
     */
    public function uname($uid = 0)
    {
        global $xoopsConfig;
        static $tblusers = [];
        $option = -1;
        if (empty($uid)) {
            $uid = $this->getVar('up_uid');
        }

        if (is_array($tblusers) && array_key_exists($uid, $tblusers)) {
            return $tblusers[$uid];
        }
        $tblusers[$uid] = \XoopsUser::getUnameFromId($uid);

        return $tblusers[$uid];
    }

    /**
     * Retourne l'URL de la page (en tenant compte des pr�f�rence d'url rewriting)
     * @param mixed $shortVersion
     * @return string L'URL
     */
    public function getURL($shortVersion = false)
    {
        $url = '';
        if (1 == Utility::getModuleOption('url_rewriting')) {
            $url = 'page-' . $this->getVar('up_pageid') . Utility::makeSeoUrl($this->getVar('up_title')) . '.html';
        } else {
            $url = 'index.php?page_id=' . $this->getVar('up_pageid');
        }
        if (!$shortVersion) {
            $url = USERPAGE_URL . $url;
        }

        return $url;
    }

    /**
     * Retourne la chaine de caract�res qui peut �tre utilis�e dans l'attribut href d'une balise html A.
     *
     * @return string
     */
    public function getHrefTitle()
    {
        return Utility::makeHrefTitle($this->getVar('up_title'));
    }

    /**
     * Prepare data for display
     *
     * @param string $format
     * @return array
     */
    public function toArray($format = 's')
    {
        $ret = [];
        foreach ($this->vars as $k => $v) {
            $ret[$k] = $this->getVar($k, $format);
        }
        $ret['up_url_rewrited']     = $this->getURL();
        $ret['up_href_title']       = $this->getHrefTitle();
        $ret['up_created_formated'] = formatTimestamp($this->getVar('up_created'), Utility::getModuleOption('dateformat'));
        $ret['user_name']           = $this->uname();

        return $ret;
    }
}
