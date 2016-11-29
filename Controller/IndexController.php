<?php

namespace Apps\CM_DigitalDownload\Controller;

use Apps\CM_DigitalDownload\Lib\Form\DataBinding\Filter;
use Apps\CM_DigitalDownload\Lib\Form\DataBinding\FilterForm;
use Apps\CM_DigitalDownload\Lib\Form\DataBinding\IFormly;
use Phpfox;
use Phpfox_Component;
use Phpfox_Database;
use Phpfox_Module;
use Phpfox_Pager;
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

		$aPages = [21, 31, 41, 51];
		$aDisplays = array();
		foreach ($aPages as $iPageCnt)
		{
			$aDisplays[$iPageCnt] = Phpfox::getPhrase('core.per_page', array('total' => $iPageCnt));
		}


		$aSort = [
			'latest' => ['d.id', _p('Latest added')],
			'most-viewed' => ['d.total_view', _p('Most viewed')],
			'most-talked' => ['d.total_comment',  _p('Most discused')]
		];

		$sDefaultOrderName = 'd.id';
		$sDefaultSort = 'DESC';
		/**
		 * @var $oFormFilter FilterForm
		 */
		$aSearchParams = [
			'type' => 'browse',
			'search_tool' => [
				'sort' => $aSort,
				'show' => $aPages,
			]
		];
		$oFormFilter = \Phpfox::getService('digitaldownload.dd')->getFilterForm();
		$oSearch = $oFormFilter->setSearch(\Phpfox::getLib('search'))->defineConditions();
		$oSearch->setCondition('`d`.`is_active` = 1');
		$oSearch->set($aSearchParams);
		$aSectionMenu = [
			_p('All') => '',
			_p('My') => 'digitaldownload.my'
		];

		$iPage = $this->request()->getInt('page');
		$iPageSize = $oSearch->getDisplay();
		$aDD = Phpfox::getService('digitaldownload.browse')->conditions($oSearch->getConditions())
			->sort($oSearch->getSort())
			->page($oSearch->getPage())
			->limit($iPageSize)
			->getCollection();
		$iCnt = $oSearch->getSearchTotal(Phpfox::getService('digitaldownload.browse')->count());
		\Phpfox_Template::instance()->buildSectionMenu('digitaldownload', $aSectionMenu);
		$this->template()
			->setTitle(_p('Digital download'))
			->setBreadCrumb(_p('Digital download'))
			->assign([
				'aDDs' => $aDD,
				'iCount' => $iCnt,
			]);
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