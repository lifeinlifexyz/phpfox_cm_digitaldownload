<?php
namespace Apps\CM_DigitalDownload\Block;


class ManagePhotos extends \Phpfox_Component
{
    public function process()
    {
        $sAssetPath = '//' . \Phpfox::getParam('core.host')
            . str_replace('/index.php', '', \Phpfox::getParam('core.folder'))
            . 'PF.Site/Apps/CM_DigitalDownload/assets/';

        $aAssets = [
            $sAssetPath . 'upload/css/jquery.fileupload.css',
            $sAssetPath . 'manage-photos.css',

            $sAssetPath . 'upload/js/jquery.iframe-transport.js',
            $sAssetPath . 'upload/js/jquery.fileupload.js',
            $sAssetPath . 'upload/js/jquery.fileupload-process.js',
            $sAssetPath . 'manage-photos.js',
        ];

        $iAllowedPhotos = 3;

        $oDD = $this->getParam('oDD');
        $aImages = $oDD['images'];

        $aPhotoData = [
            'count' => count($aImages),
            'max' => $iAllowedPhotos,
            'ddId' => $this->getParam('id'),
        ];

        $this->template()->setHeader([
            '<script type="text/javascript">window.cm_dd_image_limit=3</script>',
            '<script type="text/javascript">window.cm_dd_assets=' . json_encode($aAssets) . '</script>',
            '<script type="text/javascript">window.cm_dd_photo_data=' . json_encode($aPhotoData) . '</script>',
        ])->assign([
            'sMaxPhotosPhrase' => _p('You can add up to {{ iMax }} to your digital download.', ['iMax' => $iAllowedPhotos]),
            'aPhotos' => $aImages,
        ]);

        return 'block';
    }
}