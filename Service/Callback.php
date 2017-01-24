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
	public function __construct()
	{
		$this->_sTable = Phpfox::getT('digital_download');
	}

	public function getAjaxCommentVar()
	{
		return null;//'can_post_comment_on_dd'; todo:: uncomment
	}

	public function getCommentItem($iId)
	{
		$aDD = $this->database()->select('`id` AS comment_item_id, user_id AS comment_user_id')
			->from($this->_sTable)
			->where('`id` = ' . (int) $iId)
			->get();

		$aDD['comment_view_id'] = 1;

		return $aDD;
	}

	public function addComment($aVals, $iUserId = null, $sUserName = null)
	{
		$oDD = Phpfox::getService('digitaldownload.dd')->getDisplayer((int) $aVals['item_id']);

		if (!isset($oDD['id']))
		{
			return Phpfox_Error::trigger(_p('Invalid callback on digital download'));
		}

		(Phpfox::isModule('feed') ? Phpfox::getService('feed.process')->add($aVals['type'] . '_comment', $aVals['comment_id']) : null);

		// Update the post counter if its not a comment put under moderation or if the person posting the comment is the owner of the item.
		if (empty($aVals['parent_id']))
		{
			$this->database()->updateCounter('digital_download', 'total_comment', 'id', $aVals['item_id']);
		}

		// Send the user an email
//		$sLink = Phpfox::permalink('digitaldownload', $oDD['id']);
//
//		Phpfox::getService('comment.process')->notify(array(
//				'user_id' => $oDD['user_id'],
//				'item_id' => $oDD['id'],
//				'owner_subject' => _p('Full name commented on your digital download {{ title }}', ['full_name' => Phpfox::getUserBy('full_name'), 'title' => $oDD['title']]),
//				'owner_message' =>'test owner_message',//_p('Full name commented on your digital download  a href link title a to see the comment thread follow the link below a href link link_a',array('full_name' => Phpfox::getUserBy('full_name'), 'link' => $sLink, 'title' => $oDD['title'])),
//				'owner_notification' => 'comment.add_new_comment',
//				'notify_id' => 'comment_digitaldownload',
//				'mass_id' => 'digitaldownload',
//				'mass_subject' => (Phpfox::getUserId() == $oDD['user_id'] ?
//					Phpfox::getPhrase('marketplace.full_name_commented_on_gender_listing',array('full_name' => Phpfox::getUserBy('full_name'), 'gender' => Phpfox::getService('user')->gender($oDD['gender'], 1)))
//					:
//					Phpfox::getPhrase('marketplace.full_name_commented_on_other_full_name_s_listing',
//						array(
//							'full_name' => Phpfox::getUserBy('full_name'),
//							'other_full_name' => $oDD['full_name']
//						))),
//				'mass_message' => (Phpfox::getUserId() == $oDD['user_id'] ?
//					Phpfox::getPhrase('marketplace.full_name_commented_on_gender_listing_a_href_link_title_a_to_see_the_comment_thread_follow_the_link_below_a_href_link_link_a',array('full_name' => Phpfox::getUserBy('full_name'), 'gender' => Phpfox::getService('user')->gender($oDD['gender'], 1), 'title' => $oDD['title'], 'link' => $sLink))
//
//					:
//					Phpfox::getPhrase('marketplace.full_name_commented_on_other_full_name',array('full_name' => Phpfox::getUserBy('full_name'), 'other_full_name' => $oDD['full_name'], 'link' => $sLink, 'title' => $oDD['title']))
//
//
//				))
//		);
	}

	public function deleteComment($iId)
	{
		$this->database()->updateCounter('digital_download', 'total_comment', 'id', $iId, true);
	}

	/**
	 * Action to take when user cancelled their account
	 * @param int $iUser
	 */
	public function onDeleteUser($iUser)
	{
		$aDDs = $this->database()
			->select('id')
			->from($this->_sTable)
			->where('user_id = ' . (int)$iUser)
			->execute('getSlaveRows');

		foreach ($aDDs as &$aDD)
		{
			Phpfox::getService('digitaldownload.dd')->delete($aDD['id']);
		}

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
			Phpfox::log('Not a valid digital download.');
			
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

		$aData = json_decode($aInvoice['data'], true);
		switch ($aInvoice['type']) {
			case 'options':
				$aVal = [];
				foreach ($aData as $sOptionName => $aOption) {
					$aVal[$sOptionName] = true;
				}
				Phpfox::getService('digitaldownload.dd')->updateById($oDD['id'], $aVal);
				break;
			case 'dd':
				Phpfox::getService('digitaldownload.downloads')->add([
					'dd_id' => $aInvoice['dd_id'],
					'user_id' => $aInvoice['user_id'],
					'field' => $aData['field'],
					'limit' => ((((int)$aData['limit']) == 0) ? 9999999999: $aData['limit']),
				]);
				//todo:: notification after purchase
				break;
			default:
				Phpfox::log('Invalid type of purchase');
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