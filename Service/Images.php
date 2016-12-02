<?php

namespace Apps\CM_DigitalDownload\Service;

use Apps\CM_DigitalDownload\Lib\Form\DataBinding\FormlyTrait;
use Apps\CM_DigitalDownload\Lib\Form\DataBinding\IFormly;
use Apps\CM_DigitalDownload\Lib\Tree\Tree;
use Core\Event;
use Phpfox;
use Phpfox_File;
use Phpfox_Request;

class Images extends \Phpfox_Service
{


    public function upload($oDD)
    {
        $oFile = Phpfox_File::instance();
        $aImage = $oFile->load('image', ['jpg', 'gif', 'png']);

        if ($aImage !== false) {
            $sDirImage = Phpfox::getParam('core.dir_pic') . 'digitaldownload/';
            $sFileName = $oFile->upload('image', $sDirImage, $oDD['id'] . rand(1, 100));
            $aVal = $oDD->getRow();
            $aImages = $oDD['images'];
            $aImages[] = [
                'image_path' => $sFileName,
                'server_id' => Phpfox_Request::instance()->getServer('PHPFOX_SERVER_ID'),
            ];
            $aVal['images'] = json_encode($aImages);
            Phpfox::getService('digitaldownload.dd')->updateById($aVal['id'], $aVal);
            // thumbnails will be created automatically
            return [count($aImages), $sFileName];
        } else {
            return false;
        }

    }

   public function getImagesByDDId($iId)
   {
       $oDisplay = \Phpfox::getService('digitaldownload.dd')->getDisplayer($iId);
       return $oDisplay['images'];
   }

    /**
     * @param array $aOrders
     * @return $this
     */
    public function order($aOrders = [])
    {
        foreach($aOrders as $iKey => $aOrder) {
            $aData = [
                'parent_id' => $aOrder['parent_id'],
                'ordering' => $iKey,
            ];
            $this->database()->update(\Phpfox::getT($this->_sTable), $aData, '`category_id` = ' . $aOrder['id']);
        }
        return $this;
    }

    public function delete($iId)
    {
        Event::trigger('cd_dd_before_image_delete', $iId);
        $aImage = $this->database()->select('*')->from(Phpfox::getT($this->_sTable))->where('`image_id` = ' . $iId)->get();
        $sFilePath = Phpfox::getParam('core.dir_pic') . 'digitaldownload/' . sprintf($aImage['image_path'], '');
        Phpfox_File::instance()->unlink($sFilePath);
        $this->database()->delete(\Phpfox::getT($this->_sTable),  '`image_id` = ' . $iId);
        Event::trigger('cd_dd_after_image_delete', $iId);
        //todo:: clear cache,
    }

}