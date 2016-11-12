<?php

namespace Apps\CM_DigitalDownload\Controller\Admin;

use Apps\CM_DigitalDownload\Lib\Form\DataBinding\IFormly;
use Phpfox;
use Phpfox_Component;
use Phpfox_Plugin;

defined('PHPFOX') or exit('NO DICE!');

class CategoriesController extends Phpfox_Component
{
	public function process()
	{
		Phpfox::isAdmin();
		/**
		 * @var $oCategoryService IFormly
		 */
		$oCategoryService = Phpfox::getService('digitaldownload.category');
		$this->template()
			->setTitle(_p('Categories'))
			->setBreadCrumb(_p('Categories'))
			->assign('categories', $oCategoryService->all());
	}

	/**
	 * Garbage collector. Is executed after this class has completed
	 * its job and the template has also been displayed.
	 */
	public function clean()
	{
		(($sPlugin = Phpfox_Plugin::get('digitaldownload.component_controller_admincp_categories_clean')) ? eval($sPlugin) : false);
	}
}