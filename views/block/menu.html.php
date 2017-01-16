<?php 
/**
 * [PHPFOX_HEADER]
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond Benc
 * @package 		Phpfox
 * @version 		$Id: menu.html.php 3346 2011-10-24 15:20:05Z Raymond_Benc $
 */
 
defined('PHPFOX') or exit('NO DICE!'); 

?>
	{if ($oDD.user_id == Phpfox::getUserId() && Phpfox::getUserParam('digitaldownload.can_edit_own')) || Phpfox::getUserParam('digitaldownload.can_edit_other')}
		<li><a href="{url link='digitaldownload.add' dd_id=$oDD.id}">{_p('Edit')}</a></li>
		{*<li><a href="{url link='digitaldownload.add.customize' id=$oDD.id}">{phrase var='digitaldownload.manage_photos'}</a></li>*}
		{*<li><a href="{url link='digitaldownload.add.invite' id=$oDD.id}">{phrase var='digitaldownload.send_invitations'}</a></li>*}
		{*<li><a href="{url link='digitaldownload.add.manage' id=$oDD.id}">{phrase var='digitaldownload.manage_invites'}</a></li>*}
	{/if}
	{if ($oDD.user_id == Phpfox::getUserId() && Phpfox::getUserParam('digitaldownload.can_delete_own')) || Phpfox::getUserParam('digitaldownload.can_delete_other')}
		<li class="item_delete"><a href="{url link='digitaldownload' delete=$oDD.id}" class="sJsConfirm">{_p('Delete')}</a></li>
	{/if}	