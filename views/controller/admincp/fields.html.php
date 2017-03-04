<?php 

defined('PHPFOX') or exit('NO DICE!'); 

?>
<div class="table_header">
	{_p('Fields')}
</div>
{if count($aFields)}
<form method="post" action="{url link='digitaldownload.admincp.delete-field'}">
	<table id="_sort" data-sort-url="{url link='digitaldownload.admincp.fields.order'}" cellpadding="0" cellspacing="0">
		<tr>
			<th></th>
			<th style="width:10px;"><input type="checkbox" name="delete[]" value="" id="js_check_box_all" class="main_checkbox" /></th>
			<th style="width:20px;"></th>
			<th class="t_center" style="width:60px;">{_p('Name')}</th>
			<th>{_p('Title')}</th>
			<th>{_p('Type')}</th>
			<th>{_p('Rules')}</th>
			<th class="t_center" style="width:60px;">{_p('Active')}</th>
		</tr>
		{foreach from=$aFields key=iKey item=aItem}
		<tr class="checkRow{if is_int($iKey/2)} tr{else}{/if}" data-sort-id="{$aItem.field_id}">
			<td class="t_center">
				<i class="fa fa-sort"></i>
			</td>
			<td><input type="checkbox" name="delete[]" class="checkbox" value="{$aItem.field_id}" id="js_id_row{$aItem.field_id}" /></td>
			<td class="t_center">
				<a href="#" class="js_drop_down_link" title="Manage">{img theme='misc/bullet_arrow_down.png' alt=''}</a>
				<div class="link_menu">
					<ul>
						<li><a class="popup" href="{url link='admincp.digitaldownload.fields.add' id=$aItem.field_id}">{_p('Edit')}</a></li>
						<li><a href="{url link='digitaldownload.admincp.delete-field' delete[]=$aItem.field_id}" onclick="return confirm('{phrase var='core.are_you_sure'}');">{_p('Delete')}</a></li>
					</ul>
				</div>
			</td>
			<td class="t_center">
				{$aItem.name}
			</td>
			<td>{phrase var=$aItem.caption_phrase}</td>
			<td>{$aItem.type}</td>
			<td>{$aItem.rules}</td>
			<td class="t_center">
				<div class="js_item_is_active"{if !$aItem.is_active} style="display:none;"{/if}>
					<a href="#?call=digitaldownload.setFieldStatus&amp;id={$aItem.field_id}&amp;active=0" class="js_item_active_link" title="{_p var='Deactivate'}">{img theme='misc/bullet_green.png' alt=''}</a>
				</div>
				<div class="js_item_is_not_active"{if $aItem.is_active} style="display:none;"{/if}>
					<a href="#?call=digitaldownload.setFieldStatus&amp;id={$aItem.field_id}&amp;active=1" class="js_item_active_link" title="{_p var='Activate'}">{img theme='misc/bullet_red.png' alt=''}</a>
				</div>
			</td>
		</tr>
		{/foreach}
	</table>
	<div class="table_bottom">
		<input type="submit" value="{_p('Delete selected')}" class="sJsConfirm delete button sJsCheckBoxButton disabled" disabled="true" />
	</div>
</form>
{/if}