<?php

defined('PHPFOX') or exit('NO DICE!');

?>

<form method="post" action="{url link='admincp.digitaldownload.category.fields' id=$iId}">
    <table cellpadding="0" cellspacing="0">
        <tr>
            <th style="width:10px;"><input type="checkbox" id="js_check_box_all" class="main_checkbox" /></th>
            <th class="t_center" style="width:60px;">{_p('Name')}</th>
            <th>{_p('Title')}</th>
        </tr>
        {foreach from=$aFields key=iKey item=aItem}
        <tr class="checkRow{if is_int($iKey/2)} tr{else}{/if}">
            <td><input type="checkbox" name="catFields[]" class="checkbox"
                       value="{$aItem.field_id}" id="js_id_row{$aItem.field_id}"
                       {if in_array($aItem.field_id,$aAttachedFields) == true} checked {/if} /></td>
            <td class="t_center">
                {$aItem.name}
            </td>
            <td>{phrase var=$aItem.caption_phrase}</td>
        </tr>
        {/foreach}
    </table>
    <div class="table_bottom">
        <input type="submit" value="{_p('Save')}" class="delete button sJsCheckBoxButton disabled" disabled="true" />
    </div>
</form>