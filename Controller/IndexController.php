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

		$aPages = [21, 31, 41, 51];
		$aDisplays = array();
		foreach ($aPages as $iPageCnt)
		{
			$aDisplays[$iPageCnt] = Phpfox::getPhrase('core.per_page', array('total' => $iPageCnt));
		}

		$aSectionMenu = [
			_p('All') => '',
			_p('My') => 'digitaldownload.my',
			_p('Invoices') => 'digitaldownload.invoice',
		];

		$aSort = [
			'latest' => ['d.id', _p('Latest added')],
			'most-viewed' => ['d.total_view', _p('Most viewed')],
			'most-talked' => ['d.total_comment',  _p('Most discused')]
		];
		/**
		 * @var $oFormFilter FilterForm
		 */
		$aSearchParams = [
			'type' => 'browse',
			'search_tool' => [
				'when_field' => 'time_stamp',
				'table_alias' => 'd',
				'sort' => $aSort,
				'show' => $aPages,
				'search' => [
					'name' => 'keywords',
					'field' => Phpfox::getService('digitaldownload.field')->getFilterableFieldsName(),
					'default_value' => _p('Keywords') . '...',
					'action' => '',
				]
			]
		];

		$oSearch = \Phpfox::getLib('search')->set($aSearchParams);
		$oFormFilter = \Phpfox::getService('digitaldownload.dd')->getFilterForm();
		$oSearch = $oFormFilter->setSearch($oSearch)->defineConditions();

		switch($this->request()->get('req2')) {
			case 'my':
				Phpfox::isUser(true);
				$oSearch->setCondition('AND `d`.`user_id` = ' . \Phpfox::getUserId());
				break;
			default:
				$oSearch->setCondition('AND `d`.`is_active` = 1');

		}
		$oSearch->set($aSearchParams);


		$iPageSize = $oSearch->getDisplay();
		$aDD = Phpfox::getService('digitaldownload.browse')->conditions($oSearch->getConditions())
			->sort($oSearch->getSort())
			->page($oSearch->getPage())
			->limit($iPageSize)
			->getCollection();
		$iCnt = $oSearch->getSearchTotal(Phpfox::getService('digitaldownload.browse')->count());
		\Phpfox_Template::instance()->buildSectionMenu('digitaldownload', $aSectionMenu);

		$aAssets = [
			DD_ASSET_PATH . 'slider/css/slider.css',
			DD_ASSET_PATH . 'slider/js/bootstrap-slider.js',
			DD_ASSET_PATH . 'init-slider.js',
		];

		$this->template()
			->setHeader('<script type="text/javascript">window.cm_dd_assets=' . json_encode($aAssets) . '</script>')
			->setTitle(_p('Digital download'))
			->setBreadCrumb(_p('Digital download'))
			->assign([
				'aDDs' => $aDD,
				'iCount' => $iCnt,
				'oFilterForm' => $oFormFilter,
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