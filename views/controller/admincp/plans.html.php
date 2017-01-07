<?php 

defined('PHPFOX') or exit('NO DICE!'); 

?>
<div class="table_header">
	{_p('Plans')}
</div>
{if count($aPlans)}
<form method="post" action="{url link='digitaldownload.admincp.delete-plan'}">
	<table cellpadding="0" cellspacing="0">
		<tr>
			<th style="width:10px;"><input type="checkbox" name="delete[]" value="" id="js_check_box_all" class="main_checkbox" /></th>
			<th style="width:20px;"></th>
			<th>{_p('Name')}</th>
			<th>{_p('Price')}</th>
			<th>{_p('Allowed pictures count')}</th>
			<th>{_p('Life time(in day)')}</th>
		</tr>
		{foreach from=$aPlans key=iKey item=aItem}
		<tr class="checkRow{if is_int($iKey/2)} tr{else}{/if}" data-sort-id="{$aItem.plan_id}">
			<td><input type="checkbox" name="delete[]" class="checkbox" value="{$aItem.plan_id}" id="js_id_row{$aItem.plan_id}" /></td>
			<td class="t_center">
				<a href="#" class="js_drop_down_link" title="Manage">{img theme='misc/bullet_arrow_down.png' alt=''}</a>
				<div class="link_menu">
					<ul>
						<li><a class="popup" href="{url link='admincp.digitaldownload.plan.add' id=$aItem.plan_id}">{_p('Edit')}</a></li>
						<li><a href="{url link='digitaldownload.admincp.delete-plan' delete[]=$aItem.plan_id}" onclick="return confirm('{phrase var='core.are_you_sure'}');">{_p('Delete')}</a></li>
					</ul>
				</div>
			</td>
			<td>
				{phrase var=$aItem.name}
			</td>
			<td>
				{$aItem.price_currency_id|currency_symbol}{$aItem.price|number_format:2}
			</td>
			<td>{$aItem.allowed_count_pictures}</td>
			<td>{$aItem.life_time}</td>
		</tr>
		{/foreach}
	</table>
	<div class="table_bottom">
		<input type="submit" value="{_p('Delete selected')}" class="sJsConfirm delete button sJsCheckBoxButton disabled" disabled="true" />
	</div>
</form>
{/if}