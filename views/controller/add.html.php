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

    <div id="js_mp_block_photo" class="js_mp_block page_section_menu_holder" style="display: none">
        {module name="digitaldownload.manage-photos" id=$bEdit}
    </div>
    <div id="js_mp_block_options" class="js_mp_block page_section_menu_holder" style="display: none">
        {module name="digitaldownload.manage-options" id=$bEdit}
    </div>

    <div id="js_mp_block_invite" class="js_mp_block page_section_menu_holder" style="display:none;">
        <div class="block">
            {if Phpfox::isModule('friend')}
                <div class="title">{phrase var='marketplace.invite_friends'}</div>
                <div class="content">
                    {if $bEdit}
                    <div id="js_selected_friends" class="hide_it"></div>
                    {module name='friend.search' input='invite' hide=true friend_item_id=$bEdit friend_module_id='digitaldownload'}
                    {/if}
                </div>
            {/if}

            <div class="title">{_p('Invite People via Email')}</div>
            <div class="content">
                <textarea cols="40" rows="8" name="friend[emails]" class="form-control"></textarea>
                <div class="extra_info">
                    {_p('Separate multiple emails with a comma.')}
                    <div class="checkbox">
                        <label>
                            <input type="checkbox" name="friend[invite_from]" value="1"> {phrase var='mail.send_from_my_own_address_semail' sEmail=$sMyEmail}
                        </label>
                    </div>
                </div>
            </div>

            <div class="title">{_p('Add a Personal Message')}</div>
            <div class="content">
                <textarea cols="40" rows="8" name="friend[personal_message]" class="form-control"></textarea>
                <div class="p_top_8">
                    <input type="submit" name="do_invite" value="{phrase var='marketplace.send_invitations'}" class="button btn btn-danger" />
                </div>
            </div>
        </div>
    </div>
</form>
{section_menu_js}