<?php
/**
 * [PHPFOX_HEADER]
 */
namespace Apps\CM_DigitalDownload\Controller;

use Phpfox;
use Phpfox_Component;
use Phpfox_Error;
use Phpfox_Plugin;
use Privacy_Service_Privacy;

defined('PHPFOX') or exit('NO DICE!');

/**
 * 
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond Benc
 * @package 		Phpfox_Component
 * @version 		$Id: view.class.php 5844 2013-05-09 08:00:59Z Raymond_Benc $
 */
class ViewController extends Phpfox_Component
{
	/**
	 * Controller
	 */
	public function process()
	{
//		Phpfox::getUserParam('digitaldownload.can_view_dd', true);

		if (!($iDDId = $this->request()->get('req2'))) {
			$this->url()->send('digitaldownload');
		}

		
		if (!($oDD = Phpfox::getService('digitaldownload.dd')->getDisplayer($iDDId)))
		{
			return Phpfox_Error::display(Phpfox::getPhrase(_p('The Digital Download you are looking for either does not exist or has been removed')));
		}			
		
		$this->setParam('oDD', $oDD);
		
//		if (Phpfox::isUser() && $oDD['invite_id'] && !$oDD['visited_id'] && $oDD['user_id'] != Phpfox::getUserId())
//		{
//			Phpfox::getService('marketplace.process')->setVisit($oDD['listing_id'], Phpfox::getUserId());
//		}
		
//		if (Phpfox::isUser() && Phpfox::isModule('notification'))
//		{
//			Phpfox::getService('notification.process')->delete('comment_marketplace', $this->request()->getInt('req2'), Phpfox::getUserId());
//			Phpfox::getService('notification.process')->delete('marketplace_like', $this->request()->getInt('req2'), Phpfox::getUserId());
//		}
		
//		if (Phpfox::isModule('notification') && $oDD['user_id'] == Phpfox::getUserId())
//		{
//			Phpfox::getService('notification.process')->delete('marketplace_approved', $oDD['listing_id'], Phpfox::getUserId());
//		}
		
//		Phpfox::getService('core.redirect')->check($oDD['title']);
		if (Phpfox::isModule('privacy'))
		{
			Privacy_Service_Privacy::instance()->check('digitaldownload', $oDD['id'], $oDD['user_id'], $oDD['privacy']);
		}

		$this->setParam('aRatingCallback', array(
				'type' => 'user',
//				'default_rating' => $oDD['total_score'],
				'item_id' => $oDD['user_id'],
				'stars' => range(1, 10)
			)
		);			
		
		$this->setParam('aFeed', [
				'comment_type_id' => 'digitaldownload',
				'privacy' => $oDD['privacy'],
				'comment_privacy' => '0,1,2,3,4',//$oDD['privacy_comment'], todo::
				'like_type_id' => 'digitaldownload',
				'feed_is_liked' => $oDD['is_liked'],
				'feed_is_friend' => $oDD['is_friend'],
				'item_id' => $oDD['id'],
				'user_id' => $oDD['user_id'],
				'total_comment' => $oDD['total_comment'],
				'total_like' => $oDD['total_like'],
				'feed_link' => $this->url()->permalink('digitaldownload', $oDD['id'], (string)$oDD),
				'feed_title' => (string)$oDD,
				'feed_display' => 'view',
				'feed_total_like' => $oDD['total_like'],
				'report_module' => 'digitaldownload',
				'report_phrase' => _p('Report content')
			]
		);

//		$sExchangeRate = '';
//		if ($oDD['currency_id'] != Phpfox::getService('core.currency')->getDefault())
//		{
//			if (($sAmount = Phpfox::getService('core.currency')->getXrate($oDD['currency_id'], $oDD['price'])))
//			{
//				$sExchangeRate .= ' (' . Phpfox::getService('core.currency')->getCurrency($sAmount) . ')';
//			}
//		}
		$aMainImage = $oDD['main_image'];
		$this->template()->setTitle((string)$oDD)
			->setBreadCrumb(_p('Digitaldownload'), $this->url()->makeUrl('digitaldownload'))
			->setMeta('description', $oDD['seo_description'])
			->setMeta('keywords', $this->template()->getKeywords($oDD['seo_keyword']))
			->setMeta('og:image', Phpfox::getLib('image.helper')->display(
				[
					'server_id' => $aMainImage['server_id'],
					'path' => 'core.url_pic',
					'file' => 'digitaldownload/' . $aMainImage['image_path'],
					'suffix' => '_400',
					'return_url' => true
				]
				)
			)			
			->setBreadCrumb((string)$oDD, true)
			->setHeader('cache', [
					'jquery/plugin/star/jquery.rating.js' => 'static_script',
					'jquery/plugin/jquery.highlightFade.js' => 'static_script',
					'jquery/plugin/jquery.scrollTo.js' => 'static_script',
					'masterslider.min.js'=>'module_core',
					'switch_legend.js' => 'static_script',
					'switch_menu.js' => 'static_script',
					'view.js' => 'module_marketplace',
					'view.css' => 'module_marketplace',
					'masterslider.css' => 'module_core',
				]
			)			
			
			->setEditor([
					'load' => 'simple'
				]
			)
			->assign([
					'core_path'=>Phpfox::getParam('core.path'),
					'oDD' => $oDD,
					'aFieldNames' => $oDD->getFields([
						'description' => true,
						'privacy'=> true
					]),
				]
			);
		if (Phpfox::isModule('rate'))
		{
			$this->template()
				->setPhrase([
					'rate.thanks_for_rating'
					]
				)
				->setHeader([
					'rate.js' => 'module_rate',
					'<script type="text/javascript">$Behavior.rateDigitaldownloadUser = function() { $Core.rate.init({display: false}); }</script>',
					]
			);
		}

		$aFilterMenu = [];
//		if (!defined('PHPFOX_IS_USER_PROFILE'))
//		{
//			$sInviteTotal = '';
//			if (Phpfox::isUser() && ($iTotalInvites = Phpfox::getService('marketplace')->getTotalInvites()))
//			{
//				$sInviteTotal = '<span class="invited">' . $iTotalInvites . '</span>';
//			}
//
//			$aFilterMenu = array(
//					Phpfox::getPhrase('marketplace.all_listings') => '',
//					Phpfox::getPhrase('marketplace.my_listings') => 'my',
//					Phpfox::getPhrase('marketplace.listing_invites') . $sInviteTotal => 'invites',
//					Phpfox::getPhrase('marketplace.invoices') => 'marketplace.invoice'
//			);
//
//			if (Phpfox::getUserParam('marketplace.can_view_expired'))
//			{
//				$aFilterMenu[Phpfox::getPhrase('marketplace.expired')] = 'expired';
//			}
//			if (Phpfox::isModule('friend') && !Phpfox::getParam('core.friends_only_community'))
//			{
//				$aFilterMenu[Phpfox::getPhrase('marketplace.friends_listings')] = 'friend';
//			}
//
//			if (Phpfox::isModule('event') && Phpfox::getUserParam('event.can_approve_events'))
//			{
//				$iPendingTotal = Phpfox::getService('marketplace')->getPendingTotal();
//
//				if ($iPendingTotal)
//				{
//					$aFilterMenu[Phpfox::getPhrase('marketplace.pending_listings') . '<span class="pending">' . $iPendingTotal . '</span>'] = 'pending';
//				}
//			}
//		}
		$this->template()->buildSectionMenu('digitaldownload', $aFilterMenu);
		
		(($sPlugin = Phpfox_Plugin::get('digitaldownload.component_controller_view_process_end')) ? eval($sPlugin) : false);
        return null;
	}
	
	/**
	 * Garbage collector. Is executed after this class has completed
	 * its job and the template has also been displayed.
	 */
	public function clean()
	{
		(($sPlugin = Phpfox_Plugin::get('digitaldownload.component_controller_view_clean')) ? eval($sPlugin) : false);
	}
}