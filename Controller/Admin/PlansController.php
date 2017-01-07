<?php

namespace Apps\CM_DigitalDownload\Controller\Admin;

use Apps\CM_DigitalDownload\Lib\Form\DataBinding\IFormly;
use Phpfox;
use Phpfox_Component;
use Phpfox_Plugin;

defined('PHPFOX') or exit('NO DICE!');

class PlansController extends Phpfox_Component
{
	public function process()
	{
		Phpfox::isAdmin();
		/**
		 * @var $oPlanService IFormly
		 */
		$oPlanService = Phpfox::getService('digitaldownload.plan');
		$aDelete = $this->request()->get('delete');

		if (!empty($aDelete)) {
			foreach($aDelete as $sFieldID) {
				$oPlanService->delete($sFieldID);
			}
			$sMessage =  (count($aDelete) > 1) ? _p('Successfully deleted plans.') : _p('Successfully deleted the plan.');
			$this->url()->send('admincp.app', ['id' => 'CM_DigitalDownload'], $sMessage);
		}

		$this->template()
			->setTitle(_p('Plans'))
			->setBreadCrumb(_p('Plans'))
			->assign('aPlans', $oPlanService->all());
	}

	/**
	 * Garbage collector. Is executed after this class has completed
	 * its job and the template has also been displayed.
	 */
	public function clean()
	{
		(($sPlugin = Phpfox_Plugin::get('digitaldownload.component_controller_admincp_plans_clean')) ? eval($sPlugin) : false);
	}
}