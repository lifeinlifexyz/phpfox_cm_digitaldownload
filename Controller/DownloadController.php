<?php
/**
 * [PHPFOX_HEADER]
 */
namespace Apps\CM_DigitalDownload\Controller;

use Phpfox;
use Phpfox_Component;
use Phpfox_Error;
use Phpfox_Plugin;
use Privacy_Service_Privacy;

defined('PHPFOX') or exit('NO DICE!');

/**
 * 
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond Benc
 * @package 		Phpfox_Component
 * @version 		$Id: view.class.php 5844 2013-05-09 08:00:59Z Raymond_Benc $
 */
class DownloadController extends Phpfox_Component
{
	/**
	 * Controller
	 */
	public function process()
	{
		(($sPlugin = Phpfox_Plugin::get('digitaldownload.component_controller_download_process_start')) ? eval($sPlugin) : false);

		if (!($iDDId = $this->request()->getInt('req3'))) {
			$this->url()->send('digitaldownload');
		}

		if (!($sField = $this->request()->get('req4'))) {
			$this->url()->send('digitaldownload');
		}

		$aDDTypeFields = Phpfox::getService('digitaldownload.field')->getFieldsByType('dd');
		if (
		!($oDD = Phpfox::getService('digitaldownload.dd')->getDisplayer($iDDId))
		|| (!in_array($sField, $aDDTypeFields))
		) {
			return Phpfox_Error::display(Phpfox::getPhrase(_p('The Digital Download you are looking for either does not exist or has been removed')));
		}
		if (!$oDD->getField($sField)->canDownload()) {
			$this->url()->send('digitaldownload.' . $oDD['id'], [], _p('You must purchase this product to download'));
		}

		//if is not free and not admin and not owner then decrement limit
		if ($oDD[$sField . '_price'] != '0.00' && $oDD['user_id'] != Phpfox::getUserId()) {
			Phpfox::getService('digitaldownload.download')->decrementLimit(Phpfox::getUserId(), $iDDId, $sField);
		}

		$oDD->getField($sField)->download((string)$oDD);

		(($sPlugin = Phpfox_Plugin::get('digitaldownload.component_controller_download_process_end')) ? eval($sPlugin) : false);
        return null;
	}
	
	/**
	 * Garbage collector. Is executed after this class has completed
	 * its job and the template has also been displayed.
	 */
	public function clean()
	{
		(($sPlugin = Phpfox_Plugin::get('digitaldownload.component_controller_download_clean')) ? eval($sPlugin) : false);
	}
}