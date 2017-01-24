<?php 
/**
 * [PHPFOX_HEADER]
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond_Benc
 * @package 		Phpfox
 * @version 		$Id: purchase.html.php 1558 2010-05-04 12:51:22Z Raymond_Benc $
 */
 
defined('PHPFOX') or exit('NO DICE!'); 

?>
<div class="main_break"></div>
{if $bInvoice}

<h3>{_p('Payment methods')}</h3>
	{module name='api.gateway.form'}
{else}
<div class="info">
	<div class="info_left">
		{_p('Item you`re buying')}:
	</div>
	<div class="info_right">
		{$oDD|clean}|{$aItemField.caption}
	</div>		
</div>
<div class="info">
	<div class="info_left">
		{_p('Price')}:
	</div>
	<div class="info_right">
		{$sPrice}
	</div>		
</div>
	
<div class="separate"></div>

<div class="p_4">
	{_p('By clicking on the button below, you commit to buy this item from the seller.')}
	<div class="p_4">
		<form method="post" action="{url link='digitaldownload.purchase'}">
			<input type="hidden" name="id" value="{$oDD.id}" />
			<input type="hidden" name="process" value="1" />
			<input type="hidden" name="dd_name" value="{$sDDName}" />
			<input type="submit" value="{_p('Commit to buy')}" class="button btn-primary" />
		</form>
	</div>
</div>
{/if}