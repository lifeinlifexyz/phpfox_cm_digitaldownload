<?php 
/**
 * [PHPFOX_HEADER]
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond Benc
 * @package 		Phpfox
 * @version 		$Id: info.html.php 5844 2013-05-09 08:00:59Z Raymond_Benc $
 */
 
defined('PHPFOX') or exit('NO DICE!'); 

?>
<div class="info_holder">
	{if $oDD.description}
	<p>{$oDD.description}</p>
	{/if}
	{foreach from=$aFieldNames key=iKey item=oField}
		<div class="info">
			<div class="info_left">
				{$oField.caption}:
			</div>
			<div class="info_right">
				{$oField.value}
			</div>
		</div>
	{/foreach}
</div>