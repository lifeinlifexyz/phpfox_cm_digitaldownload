<?php
/**
 * [PHPFOX_HEADER]
 */

namespace Apps\CM_DigitalDownload\Controller;

use Phpfox;
use Phpfox_Component;
use Phpfox_Plugin;

defined('PHPFOX') or exit('NO DICE!');

define('PHPFOX_SKIP_POST_PROTECTION', true);

/**
 * 
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond_Benc
 * @package 		Phpfox_Component
 * @version 		$Id: index.class.php 1558 2010-05-04 12:51:22Z Raymond_Benc $
 */
class InvoiceController extends Phpfox_Component
{
	/**
	 * Controller
	 */
	public function process()
	{
		Phpfox::isUser(true);
		//add button to add new Digital Download
		if (user('digitaldownload.cm_dd_add' , '0') == '1') {
			sectionMenu(_p('Add'), url('/digitaldownload/add'));
		}
		$aCond = [];
		
		$aCond[] = 'AND di.user_id = ' . Phpfox::getUserId();

		list($iCnt, $aInvoices) = Phpfox::getService('digitaldownload.invoice')->getInvoices($aCond);

		$aFilterMenu = [];
		if (!defined('PHPFOX_IS_USER_PROFILE'))
		{
			$aFilterMenu = [
				_p('All Files') => '',
				_p('My Files') => 'digitaldownload.my',
				_p('Friends` Files') => 'digitaldownload.friends',
				_p('Invoices') => 'digitaldownload.invoice',
			];

			if (Phpfox::getUserParam('digitaldownload.can_moderate')) {
				$aFilterMenu[_p('Moderation')] =  'digitaldownload.moderation';
			}

			if (Phpfox::getUserParam('digitaldownload.can_view_expired')) {
				$aFilterMenu[_p('Expired')] = 'digitaldownload.expired';
			}
		}

		$this->template()->setTitle(_p('Invoices'))
			->setBreadCrumb(_p('Digital Download'), $this->url()->makeUrl('digitaldownload'))
			->assign([
					'aInvoices' => $aInvoices
				]
			);

		$this->template()->buildSectionMenu('digitaldownload', $aFilterMenu);
	}
	
	/**
	 * Garbage collector. Is executed after this class has completed
	 * its job and the template has also been displayed.
	 */
	public function clean()
	{
		(($sPlugin = Phpfox_Plugin::get('digitaldownload.component_controller_invoice_index_clean')) ? eval($sPlugin) : false);
	}
}