<?php
/**
 * [PHPFOX_HEADER]
 */

namespace Apps\CM_DigitalDownload\Service;

use Phpfox;
use Phpfox_Error;
use Phpfox_Plugin;
use Phpfox_Service;
use Phpfox_Url;

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
		$sLink = Phpfox::permalink('digitaldownload', $oDD['id']);
		$sMassMessage = Phpfox::getUserId() == $oDD['user_id']
			? _p('{full_name} commented on {gender} digital download "<a href="{link}">{title}</a>". To see the comment thread, follow the link below: <a href="{link}">{link}</a>',
				[
					'full_name' => Phpfox::getUserBy('full_name'),
					'gender' => Phpfox::getService('user')->gender($oDD['gender'], 1),
					'title' => (string)$oDD,
					'link' => $sLink])
			: _p('{full_name} commented on {other_full_name}\'s listing "<a href="{link}">{title}</a>". To see the comment thread, follow the link below: <a href="{link}">{link}</a>', [
				'full_name' => Phpfox::getUserBy('full_name'),
				'other_full_name' => $oDD['full_name'],
				'link' => $sLink, 'title' => (string)$oDD]);
		$aMassSubject = Phpfox::getUserId() == $oDD['user_id']
			? _p('{full_name} commented on {gender} listing.', [
				'full_name' => Phpfox::getUserBy('full_name'),
				'gender' => Phpfox::getService('user')->gender($oDD['gender'], 1)
			])
			:
			_p('{full_name} commented on {other_full_name}\'s listing.', [
				'full_name' => Phpfox::getUserBy('full_name'),
				'other_full_name' => $oDD['full_name']
			]);

		Phpfox::getService('comment.process')->notify([
				'user_id' => $oDD['user_id'],
				'item_id' => $oDD['id'],
				'owner_subject' => _p('{full_name} commented on your digital download {{ title }}', ['full_name' => Phpfox::getUserBy('full_name'), 'title' => (string)$oDD]),
				'owner_message' =>_p('{full_name} commented on your digital download  a href link title a to see the comment thread follow the link below a href link link_a', ['full_name' => Phpfox::getUserBy('full_name'), 'link' => $sLink, 'title' => (string)$oDD]),
				'owner_notification' => 'comment.add_new_comment',
				'notify_id' => 'comment_digitaldownload',
				'mass_id' => 'digitaldownload',
				'mass_subject' => $aMassSubject,
				'mass_message' => $sMassMessage]
		);
	}



	public function deleteComment($iId)
	{
		$this->database()->updateCounter('digital_download', 'total_comment', 'id', $iId, true);
	}

	public function getFeedRedirect($iId, $iChild = null)
	{
		(($sPlugin = Phpfox_Plugin::get('digitaldownload.service_callback_getfeedredirect')) ? eval($sPlugin) : false);

		return Phpfox::permalink('digitaldownload', $iId);
	}

	public function getReportRedirect($iId)
	{
		return $this->getFeedRedirect($iId);
	}

	public function getRedirectComment($iId)
	{
		return $this->getFeedRedirect($iId);
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
		try {
			Phpfox::log('Module callback recieved: ' . var_export($aParams, true));
			Phpfox::log('Attempting to retrieve purchase from the database');

			$aInvoice = Phpfox::getService('digitaldownload.invoice')->get($aParams['item_number']);

			if ($aInvoice === false)
			{
				Phpfox::log('Not a valid invoice');

				return false;
			}

			$aDD = Phpfox::getService('digitaldownload.dd')->getForEdit($aInvoice['dd_id']);

			if ($aDD === false)
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
			$this->database()->beginTransaction();
			$this->database()->update(Phpfox::getT('digital_download_invoice'), array(
				'status' => $aParams['status'],
				'time_stamp_paid' => PHPFOX_TIME
			), 'invoice_id = ' . $aInvoice['invoice_id']
			);

			$aData = json_decode($aInvoice['data'], true);
			switch ($aInvoice['type']) {
				case 'options':

					$aVal = [];
					$aOptionCaptions = [];
					foreach ($aData as $sOptionName => &$aOption) {
						$aVal[$sOptionName] = true;
						$aOptionCaptions[]  = $aOption['caption'];
					}

					$bActivate = isset($aVal['activate']);

					if ($bActivate) { //activate and set expire time

						$aVal['is_active'] = true;
						unset($aVal['activate']);
						Phpfox::log('Activate DD');
						$aPlan = Phpfox::getService('digitaldownload.dd')->getPlan($aInvoice['dd_id']);
						$iLifeDays = (!isset($aPlan['life_time']) || empty($aPlan['life_time']))
							? 365 * 10
							: (int)$aPlan['life_time'];
						$aVal['expire_timestamp'] = PHPFOX_TIME + (60 * 60 * 24 * $iLifeDays);
					}

					($bActivate && ($sPlugin = Phpfox_Plugin::get('digitaldownload.before_activate_digitaldownload')) ? eval($sPlugin) : false);
					Phpfox::log('Update with data:' . var_export($aVal, true));
					Phpfox::getService('digitaldownload.dd')->updateById($aInvoice['dd_id'], $aVal);
					Phpfox::log('Updated DD');


					//send email to admins
					$oDD = 	Phpfox::getService('digitaldownload.dd')->setRow($aDD)
						->getDisplayer($aDD['id']);
					$sTitle = (string)$oDD;
					$aEmail = [
						'user_id' => $this->database()->select('*')->from(Phpfox::getT('user'))->where('user_group_id = 1')->all(),
						'title' => _p('Options: ') . implode(', ', $aOptionCaptions),
						'subject_title' => $sTitle,
					];

					($bActivate && ($sPlugin = Phpfox_Plugin::get('digitaldownload.after_activate_digitaldownload')) ? eval($sPlugin) : false);
					break;
				case 'dd':
					Phpfox::getService('digitaldownload.download')->add([
						'dd_id' => $aInvoice['dd_id'],
						'user_id' => $aInvoice['user_id'],
						'field' => $aData['field'],
						'limit' => ((((int)$aData['limit']) == 0) ? 9999999999: $aData['limit']),
					]);
					$this->database()->updateCounter('digital_download', 'total_download', 'id', $aInvoice['dd_id']);
					(($sPlugin = Phpfox_Plugin::get('digitaldownload.after_paid_for_digitaldownload')) ? eval($sPlugin) : false);

					Phpfox::getService('notification.process')
						->add('digitaldownload', $aInvoice['invoice_id'],  $aInvoice['user_id'], $aDD['user_id']);

					$oDD = 	Phpfox::getService('digitaldownload.dd')->setRow($aDD)
						->getDisplayer($aDD['id']);
					$sTitle = (string)$oDD;
					$aEmail = [
						'user_id' => $aDD['user_id'],
						'title' => $sTitle,
						'subject_title' => $sTitle,
					];
					break;
				default:
					if ($sPlugin = Phpfox_Plugin::get('digitaldownload.invoice_extra_type_payment_callback')) {
						eval($sPlugin);
					} else {
						Phpfox::log('Invalid type of purchase');
					}
			}


			Phpfox::getLib('mail')->to($aEmail['user_id'])
				->subject(['digitaldownload_item_sold_title', ['title' => Phpfox::getLib('parse.input')->clean($aEmail['subject_title'], 255)]])
				->fromName($aInvoice['full_name'])
				->message(['digitaldownload_full_name_has_purchased_an_item_of_yours_on_site_name', [
						'full_name' => $aInvoice['full_name'],
						'site_name' => Phpfox::getParam('core.site_title'),
						'title' => $aEmail['title'],
						'link' => Phpfox_Url::instance()->makeUrl('digitaldownload.view', $aDD['id']),
						'user_link' => Phpfox_Url::instance()->makeUrl($aInvoice['user_name']),
						'price' => Phpfox::getService('core.currency')->getCurrency($aInvoice['price'], $aInvoice['currency_id'])
					]
					]
				)
				->send();

			Phpfox::log('Handling complete');
			$this->database()->commit();
		} catch (\Exception $e) {
			$this->database()->rollback();
			Phpfox::log("Error: " . $e->getMessage() . "; File: " . $e->getFile() . "; Line" .  $e->getFile());
		}
	}

	//notify user about access dd to download
	public function getNotification($aNotification)
	{
		$aDD = $this->database()
			->select('d.*, i.*')
			->from(Phpfox::getT('digital_download_invoice'), 'i')
			->join(Phpfox::getT('digital_download'), 'd', 'd.id = i.dd_id')
			->where('i.invoice_id = ' . $aNotification['item_id'])
			->get();

		if (!isset($aDD['id'])) {
			return false;
		}

		$oDD = 	Phpfox::getService('digitaldownload.dd')->setRow($aDD)
			->getDisplayer($aDD['id']);

		$sTitle = (string) $oDD;

		$sMessage = _p('You are available for download {dd}', ['dd' => $sTitle]);

		return [
			'link'    =>  $oDD['url'],
			'message' => $sMessage,
			'icon'    => '',
		];

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