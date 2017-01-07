<?php 

defined('PHPFOX') or exit('NO DICE!'); 

?>
<form method="post" action="{url link='current'}" enctype="multipart/form-data" id="js_marketplace_form">
    <div><input type="hidden" name="page_section_menu" value="" id="page_section_menu_form" /></div>

    <div id="js_mp_block_detail" class="js_mp_block page_section_menu_holder">
        {$sFieldsHtml}
        <div class="form-group">
            <input class="btn btn-primary" type="submit" value="{if $bEdit}{_p('Save')}{else}{_p('Add')}{/if}">
        </div>
    </div>

    <div id="js_mp_block_photo" class="js_mp_block page_section_menu_holder">
        {module name="digitaldownload.managephotos" id=$bEdit}
    </div>
</form>
{section_menu_js}