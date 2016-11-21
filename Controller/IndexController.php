<?php

namespace Apps\CM_DigitalDownload\Controller;

use Apps\CM_DigitalDownload\Lib\Form\DataBinding\IFormly;
use Phpfox;
use Phpfox_Component;
use Phpfox_Plugin;

defined('PHPFOX') or exit('NO DICE!');

class IndexController extends Phpfox_Component
{
	public function process()
	{

		//add button to add new Digital Download
		if (user('cm_dd_add' , '0') == '1') {
			sectionMenu(_p('Add a Digital Download'), url('/digitaldownload/add'));
		}

		$this->template()
			->setTitle(_p('Digital download'))
			->setBreadCrumb(_p('Digital download'));
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