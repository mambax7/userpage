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
 * Class PageHandler
 * @package XoopsModules\Userpage
 */
class PageHandler extends \XoopsObjectHandler
{
    /**
     * @param bool $isNew
     * @return \XoopsModules\Userpage\Page|\XoopsObject
     */
    public function &create($isNew = true)
    {
        $pageObj = new Page();
        if ($isNew) {
            $pageObj->setNew();
        }

        return $pageObj;
    }

    /**
     * @param int $id
     * @return \XoopsModules\Userpage\Page|\XoopsObject|null
     */
    public function &get($id)
    {
        $ret = null;
        $sql = 'SELECT * FROM ' . $this->db->prefix('userpage') . ' WHERE up_pageid=' . (int)$id;
        if (!$result = $this->db->query($sql)) {
            return $ret;
        }
        $numrows = $this->db->getRowsNum($result);
        if (1 == $numrows) {
            $pageObj = new Page();
            $pageObj->assignVars($this->db->fetchArray($result));

            return $pageObj;
        }

        return $ret;
    }

    /**
     * @param \XoopsObject $pageObj
     * @param bool         $force
     * @return bool|int|void
     */
    public function insert(\XoopsObject $pageObj, $force = false)
    {
        if (Page::class !== get_class($pageObj)) {
            return false;
        }
        if (!$pageObj->isDirty()) {
            return true;
        }
        if (!$pageObj->cleanVars()) {
            foreach ($pageObj->getErrors() as $oneerror) {
                trigger_error($oneerror);
            }

            return false;
        }
        foreach ($pageObj->cleanVars as $k => $v) {
            ${$k} = $v;
        }
        if ($pageObj->isNew()) {
            $format = 'INSERT INTO %s (up_pageid, up_uid, up_title, up_text, up_created, up_hits) VALUES (%u, %u, %s, %s, %u, %u)';
            $sql    = sprintf($format, $this->db->prefix('userpage'), $this->db->genId($this->db->prefix('userpage') . '_up_pageid_seq'), $up_uid, $this->db->quoteString($up_title), $this->db->quoteString($up_text), $up_created, $up_hits);
            $force  = true;
        } else {
            $format = 'UPDATE %s SET up_uid=%d, up_title=%s, up_text=%s, up_created=%u, up_hits=%u WHERE up_pageid = %u';
            $sql    = sprintf($format, $this->db->prefix('userpage'), $up_uid, $this->db->quoteString($up_title), $this->db->quoteString($up_text), $up_created, $up_hits, $up_pageid);
        }
        if ($force) {
            $result = $this->db->queryF($sql);
        } else {
            $result = $this->db->query($sql);
        }
        if (!$result) {
            return false;
        }
        if (empty($up_pageid)) {
            $up_pageid = $this->db->getInsertId();
        }
        $pageObj->assignVar('up_pageid', $up_pageid);

        return $up_pageid;
    }

    /**
     * Remove the specified object
     *
     * @param \XoopsObject $pageObj
     * @param bool         $force
     * @return bool
     */
    public function delete(\XoopsObject $pageObj, $force = false)
    {
        global $xoopsModule;
        if (Page::class !== get_class($pageObj)) {
            return false;
        }
        $up_pageid = $pageObj->getVar('up_pageid');
        $sql       = sprintf('DELETE FROM %s WHERE up_pageid = %u', $this->db->prefix('userpage'), $up_pageid);
        if (false !== $force) {
            $result = $this->db->queryF($sql);
        } else {
            $result = $this->db->query($sql);
        }
        // Suppression des commentaires associ�s
        $mid = $xoopsModule->getVar('mid');
        xoops_comment_delete($mid, $up_pageid);

        if (!$result) {
            return false;
        }

        return true;
    }

    /**
     * @param \CriteriaElement $criteria
     * @param bool             $id_as_key
     * @return array
     */
    public function &getObjects(\CriteriaElement $criteria = null, $id_as_key = false)
    {
        $ret   = [];
        $limit = $start = 0;
        $sql   = 'SELECT * FROM ' . $this->db->prefix('userpage');
        if (isset($criteria) && $criteria instanceof \criteriaelement) {
            $sql .= ' ' . $criteria->renderWhere();
            if ('' != $criteria->getSort()) {
                $sql .= ' ORDER BY ' . $criteria->getSort() . ' ' . $criteria->getOrder();
            }
            $limit = $criteria->getLimit();
            $start = $criteria->getStart();
        }
        $result = $this->db->query($sql, $limit, $start);
        if (!$result) {
            return $ret;
        }
        while (false !== ($myrow = $this->db->fetchArray($result))) {
            if (!$id_as_key) {
                $ret[] = new Page($myrow);
            } else {
                $ret[$myrow['up_pageid']] = new Page($myrow);
            }
        }

        return $ret;
    }

    /**
     * Function used to return random pages
     * @param null|mixed $criteria
     * @param mixed      $id_as_key
     * @param mixed      $items_count
     * @return array
     */
    public function getRandomPages($criteria = null, $id_as_key = false, $items_count = 10)
    {
        $ret   = $rand_keys = $ret3 = [];
        $limit = $start = 0;
        $sql   = 'SELECT up_pageid FROM ' . $this->db->prefix('userpage');
        if (isset($criteria) && is_subclass_of($criteria, 'criteriaelement')) {
            $sql .= ' ' . $criteria->renderWhere();
            if ('' != $criteria->getSort()) {
                $sql .= ' ORDER BY ' . $criteria->getSort() . ' ' . $criteria->getOrder();
            }
            $limit = $criteria->getLimit();
            $start = $criteria->getStart();
        }
        $result = $this->db->query($sql, $limit, $start);
        if (!$result) {
            return $ret;
        }
        while (false !== ($myrow = $this->db->fetchArray($result))) {
            $ret[] = $myrow['up_pageid'];
        }
        $cnt = count($ret);
        if ($cnt) {
            if ($items_count > $cnt) {
                $items_count = $cnt;
            }
            $rand_keys = array_rand($ret, $items_count);
            if ($items_count > 1) {
                for ($i = 0; $i < $items_count; ++$i) {
                    $onepage = $ret[$rand_keys[$i]];
                    $ret3[]  = new Page($onepage);
                }
            } else {
                $ret3[] = new Page($ret[$rand_keys]);
            }
        }

        return $ret3;
    }

    /**
     * @param null $criteria
     * @return int
     */
    public function getCount($criteria = null)
    {
        $sql = 'SELECT COUNT(*) FROM ' . $this->db->prefix('userpage');
        if (isset($criteria) && $criteria instanceof \criteriaelement) {
            $sql .= ' ' . $criteria->renderWhere();
        }
        $result = $this->db->query($sql);
        if (!$result) {
            return 0;
        }
        list($count) = $this->db->fetchRow($result);

        return $count;
    }

    /**
     * @param null $criteria
     * @return bool
     */
    public function deleteAll($criteria = null)
    {
        $sql = 'DELETE FROM ' . $this->db->prefix('userpage');
        if (isset($criteria) && $criteria instanceof \CriteriaElement) {
            $sql .= ' ' . $criteria->renderWhere();
        }
        if (!$result = $this->db->queryF($sql)) {
            return false;
        }

        return true;
    }

    /**
     * Update current page's counter
     * @param mixed $up_pageid
     * @return bool
     */
    public function UpdateCounter($up_pageid)
    {
        $sql = 'UPDATE ' . $this->db->prefix('userpage') . ' SET up_hits=up_hits+1 WHERE up_pageid = ' . (int)$up_pageid;
        if ($this->db->queryF($sql)) {
            return true;
        }

        return false;
    }
}
