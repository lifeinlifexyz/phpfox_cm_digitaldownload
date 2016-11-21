<?php

namespace Apps\CM_DigitalDownload\Service;

use Apps\CM_DigitalDownload\Lib\Form\DataBinding\FormlyTrait;
use Apps\CM_DigitalDownload\Lib\Form\DataBinding\IFormly;
use Apps\CM_DigitalDownload\Lib\Tree\Tree;

class CategoryField extends \Phpfox_Service
{

    protected $_sTable = 'digital_download_category_fields';

    /**
     *  attache fields to category
     */
    public function sync($aFieldIds = [], $iCatId)
    {
        $this->database()->delete(\Phpfox::getT($this->_sTable), '`category_id` = ' . $iCatId);
        foreach($aFieldIds as $iId) {
            $this->database()->insert(\Phpfox::getT($this->_sTable), [
                'category_id' => $iCatId,
                'field_id' => $iId,
            ]);
        }
    }

    public function getByCategoryId($iId)
    {
        return $this->database()
            ->select('*')
            ->from(\Phpfox::getT($this->_sTable))
            ->where('`category_id` = ' . $iId)
            ->execute('getslaverows');
    }

    public function getInfoByCategoryId($iId)
    {
        return $this->database()
            ->select('f.*')
            ->from(\Phpfox::getT($this->_sTable), 'c')
            ->leftJoin(\Phpfox::getT('digital_download_fields'), 'f', 'f.field_id = c.field_id')
            ->where('`category_id` = ' . $iId)
            ->order('`ordering` ASC')
            ->execute('getslaverows');
    }

    public function getByFieldId($iId)
    {
        return $this->database()
            ->select('*')
            ->from(\Phpfox::getT($this->_sTable))
            ->where('`field_id` = ' . $iId)
            ->execute('getslaverows');
    }

    public function getList()
    {
        return $this->database()
            ->select("*")
            ->from(\Phpfox::getT($this->_sTable))
            ->order("`ordering` ASC")
            ->execute('getslaverows');
    }

    public function delete($iId)
    {
        $this->database()->delete(\Phpfox::getT($this->_sTable),  '`category_id` = ' . $iId);
        //todo:: trigger event after category deleted
    }

}