<?php

namespace Apps\CM_DigitalDownload\Controller\Admin;

use Apps\CM_DigitalDownload\Lib\Form\DataBinding\IFormly;
use Phpfox;
use Phpfox_Component;
use Phpfox_Plugin;

defined('PHPFOX') or exit('NO DICE!');

class AddCategoryController extends Phpfox_Component
{
	public function process()
	{
		$bIsEdit = false;
		$bIsSub = false;
//        $aLanguages = \Language_Service_Language::instance()->getAll();
		/**
		 * @var $oCategoryService IFormly
		 */
		$oCategoryService = Phpfox::getService('digitaldownload.category');
		$oForm = $oCategoryService->getForm([
			'action' => $this->url()->makeUrl('current'),
		]);
//		if (($iSubtEditId = $this->request()->getInt('sub'))) {
//			$aRow = Phpfox::getService('groups.category')->getForEdit($iSubtEditId);
//			$iEditId = $iSubtEditId;
//			$bIsEdit = true;
//			$bIsSub = true;
//			$this->template()->assign([
//					'aForms'  => $aRow,
//					'iEditId' => $iEditId,
//				]
//			);
//		}
//
		if ($_POST && $oForm->isValid()) {
			$oForm->save();
		}

		$this->template()
			->setTitle(_p('Add category'))
			->setBreadCrumb(_p('Add category'))
			->assign([
					'form' => $oForm,
				]
			);
	}

	/**
	 * Garbage collector. Is executed after this class has completed
	 * its job and the template has also been displayed.
	 */
	public function clean()
	{
		(($sPlugin = Phpfox_Plugin::get('digitaldownload.component_controller_admincp_add_clean')) ? eval($sPlugin) : false);
	}
}