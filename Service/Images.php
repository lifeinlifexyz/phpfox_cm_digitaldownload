<?php

namespace Apps\CM_DigitalDownload\Service;

use Apps\CM_DigitalDownload\Lib\Form\DataBinding\FormlyTrait;
use Apps\CM_DigitalDownload\Lib\Form\DataBinding\IFormly;
use Apps\CM_DigitalDownload\Lib\Tree\Tree;
use Core\Event;
use Phpfox;
use Phpfox_File;
use Phpfox_Plugin;
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
            return [count($aImages) - 1, $sFileName];
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

    public function deleteDDImages($iDDId)
    {
        (($sPlugin = Phpfox_Plugin::get('digitaldownload.before_delete_dd_images')) ? eval($sPlugin) : false);
        $aImages = $this->getImagesByDDId($iDDId);
        $aSizes = ['', 50, 120, 200, 400];
        $iFileSizes = 0;
        foreach ($aImages as &$aImage)
        {
            foreach ($aSizes as $iSize)
            {
                $sImage = Phpfox::getParam('core.dir_pic') . sprintf($aImage['image_path'], (empty($iSize) ? '' : '_' ) . $iSize);
                if (file_exists($sImage)) {
                    $iFileSizes += filesize($sImage);

                    Phpfox_File::instance()->unlink($sImage);
                }
            }
        }
        (($sPlugin = Phpfox_Plugin::get('digitaldownload.after_delete_dd_images')) ? eval($sPlugin) : false);
        //todo:: clear cache,
    }

}