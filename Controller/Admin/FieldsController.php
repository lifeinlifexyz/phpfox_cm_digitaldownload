<?php

namespace Apps\CM_DigitalDownload\Controller\Admin;

use Apps\CM_DigitalDownload\Lib\Form\DataBinding\IFormly;
use Phpfox;
use Phpfox_Component;
use Phpfox_Plugin;

defined('PHPFOX') or exit('NO DICE!');

class FieldsController extends Phpfox_Component
{
	public function process()
	{
		Phpfox::isAdmin();
		/**
		 * @var $oFieldService IFormly
		 */
		$oFieldService = Phpfox::getService('digitaldownload.field');
		$aDelete = $this->request()->get('delete');

		if (!empty($aDelete)) {
			foreach($aDelete as $sFieldID) {
				$oFieldService->delete($sFieldID);
			}
			$sMessage =  (count($aDelete) > 1) ? _p('Successfully deleted fields.') : _p('Successfully deleted the field.');
			$this->url()->send('admincp.app', ['id' => 'CM_DigitalDownload'], $sMessage);
		}

		$this->template()
			->setTitle(_p('Fields'))
			->setBreadCrumb(_p('Fields'))
			->assign('aFields', $oFieldService->all());
	}

	/**
	 * Garbage collector. Is executed after this class has completed
	 * its job and the template has also been displayed.
	 */
	public function clean()
	{
		(($sPlugin = Phpfox_Plugin::get('digitaldownload.component_controller_admincp_fields_clean')) ? eval($sPlugin) : false);
	}
}