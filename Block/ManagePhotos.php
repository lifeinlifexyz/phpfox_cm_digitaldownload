<?php
namespace Apps\CM_DigitalDownload\Block;


class ManagePhotos extends \Phpfox_Component
{
    public function process()
    {
        $aAssets = [
            DD_ASSET_PATH . 'upload/css/jquery.fileupload.css',
            DD_ASSET_PATH . 'manage-photos.css',

            DD_ASSET_PATH . 'upload/js/jquery.iframe-transport.js',
            DD_ASSET_PATH . 'upload/js/jquery.fileupload.js',
            DD_ASSET_PATH . 'upload/js/jquery.fileupload-process.js',
            DD_ASSET_PATH . 'manage-photos.js',
        ];
        $aPlan = $this->getParam('aPlan');
        $iAllowedPhotos = $aPlan['allowed_count_pictures'];

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
            'sMaxPhotosPhrase' => _p('dd_allowed_max_photo_count', ['iMax' => $iAllowedPhotos]),
            'aPhotos' => $aImages,
        ]);

        return 'block';
    }
}