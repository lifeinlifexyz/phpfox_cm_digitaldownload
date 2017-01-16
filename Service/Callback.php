<?php
/**
 * [PHPFOX_HEADER]
 */

namespace Apps\CM_DigitalDownload\Service;

use Phpfox;
use Phpfox_Error;
use Phpfox_Plugin;
use Phpfox_Service;

defined('PHPFOX') or exit('NO DICE!');

/**
 * 
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond Benc
 * @package 		Phpfox_Service
 * @version 		$Id: callback.class.php 7059 2014-01-22 14:20:10Z Fern $
 */
class Callback extends Phpfox_Service
{

	public function getAjaxCommentVar()
	{
		return null;//'can_post_comment_on_dd';
	}

	public function getCommentItem($iId)
	{
		$aListing = $this->database()->select('id AS comment_item_id, user_id AS comment_user_id')
			->from(\Phpfox::getT('digital_download'))
			->where('id = ' . (int) $iId)
			->get();

		$aListing['comment_view_id'] = 1;

		return $aListing;
	}

	public function paymentApiCallback($aParams)
	{
		Phpfox::log('Module callback recieved: ' . var_export($aParams, true));	
		Phpfox::log('Attempting to retrieve purchase from the database');		
		
		$aInvoice = Phpfox::getService('digitaldownload.invoice')->get($aParams['item_number']);
		
		if ($aInvoice === false)
		{
			Phpfox::log('Not a valid invoice');
			
			return false;
		}
		
		$oDD = Phpfox::getService('digitaldownload.dd')->getDisplayer($aInvoice['dd_id']);
		
		if ($oDD === false)
		{
			Phpfox::log('Not a valid listing.');
			
			return false;
		}
		
		Phpfox::log('Purchase is valid: ' . var_export($aInvoice, true));
		
		if ($aParams['status'] == 'completed')
		{
			if ($aParams['total_paid'] == $aInvoice['price'])
			{
				Phpfox::log('Paid correct price');
			}
			else 
			{
				Phpfox::log('Paid incorrect price');
				
				return false;
			}
		}
		else 
		{
			Phpfox::log('Payment is not marked as "completed".');
			
			return false;
		}
		
		Phpfox::log('Handling purchase');
		
		$this->database()->update(Phpfox::getT('digital_download_invoice'), array(
				'status' => $aParams['status'],
				'time_stamp_paid' => PHPFOX_TIME
			), 'invoice_id = ' . $aInvoice['invoice_id']
		);		


		if ($aInvoice['type'] == 'options')
		{
			$aOptions = json_decode($aInvoice['data']);
			$aVal = [];
			foreach ($aOptions as $sOptionName => $aOption) {
				$aVal[$sOptionName] = true;
			}
			Phpfox::getService('digitaldownload.dd')->updateById($oDD['id'], $aVal);
		}
		
//		Phpfox::getLib('mail')->to($oDD['user_id'])
//			->subject(array('marketplace.item_sold_title', array('title' => Phpfox::getLib('parse.input')->clean($oDD['title'], 255))))
//			->fromName($aInvoice['full_name'])
//			->message(array('marketplace.full_name_has_purchased_an_item_of_yours_on_site_name', array(
//						'full_name' => $aInvoice['full_name'],
//						'site_name' => Phpfox::getParam('core.site_title'),
//						'title' => $oDD['title'],
//						'link' => Phpfox_Url::instance()->makeUrl('marketplace.view', $oDD['title_url']),
//						'user_link' => Phpfox_Url::instance()->makeUrl($aInvoice['user_name']),
//						'price' => Phpfox::getService('core.currency')->getCurrency($aInvoice['price'], $aInvoice['currency_id'])
//					)
//				)
//			)
//			->send();
		
		Phpfox::log('Handling complete');		
	}
	
	/**
	 * If a call is made to an unknown method attempt to connect
	 * it to a specific plug-in with the same name thus allowing 
	 * plug-in developers the ability to extend classes.
	 *
	 * @param string $sMethod is the name of the method
	 * @param array $aArguments is the array of arguments of being passed
	 * @return mixed
	 */
	public function __call($sMethod, $aArguments)
	{
		/**
		 * Check if such a plug-in exists and if it does call it.
		 */
		if ($sPlugin = Phpfox_Plugin::get('digitaldownload.service_callback__call'))
		{
			eval($sPlugin);
            return null;
		}
			
		/**
		 * No method or plug-in found we must throw a error.
		 */
		Phpfox_Error::trigger('Call to undefined method ' . __CLASS__ . '::' . $sMethod . '()', E_USER_ERROR);
	}	
}