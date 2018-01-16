<?php
/**
 * [PHPFOX_HEADER]
 *
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond_Benc
 * @package 		Phpfox
 * @version 		$Id: index.html.php 1594 2010-05-22 22:49:41Z Raymond_Benc $
 */

defined('PHPFOX') or exit('NO DICE!');

?>
{if !count($aInvoices)}
<div class="extra_info">
	{_p('You do not have any invoices')}
</div>
{else}
<table class="default_table" cellpadding="0" cellspacing="0">
	<tr>
		<th>{_p('Id')}</th>
		<th>{_p('Status')}</th>
		<th>{_p('Price')}</th>
		<th>{_p('Date')}</th>
	</tr>
	{foreach from=$aInvoices item=aInvoice}
	<tr id="data-invoice-id-{$aInvoice.invoice_id}">
		<td class="t_center">{$aInvoice.invoice_id}</td>
		<td>{$aInvoice.status_phrase}{if $aInvoice.status === null || $aInvoice.status == 'pending'} ( <a href="{url link='digitaldownload.purchase' invoice=$aInvoice.invoice_id}">{phrase var='Pay now'}</a> | <a href="#" onclick="{literal}$.ajaxCall('digitaldownload.deleteInvoice', 'invoice={/literal}{$aInvoice.invoice_id}{literal}');{/literal}">{phrase var='Cancel'}</a> ){/if}</td>
		<td>{$aInvoice.price|currency:$aInvoice.currency_id}</td>
		<td>{$aInvoice.time_stamp|date}</td>
	</tr>
	{/foreach}
</table>
{/if}