<div class="dd-manage-photos">
    <p class="alert alert-info">{$sMaxPhotosPhrase}</p>
    <ul class="pictures">
        {foreach from=$aPhotos name=dd_photos item="aItem"}
        <li id="dd_photo_{$aItem.image_id}">
            <div class="picture">
                {img path='core.url_pic' file='digitaldownload/'.$aItem.image_path server_id=$aItem.server_id suffix='_150' max_width=150}
            </div>
            <a title="{_p('Delete')}" data-id="{$aItem.image_id}" data-dd-id="{$bEdit}" class="item-controls delete">
                <i class="fa fa-remove"></i>
            </a>
        </li>
        {/foreach}
        <li class="add-photo-form">
            <div>
                <div class="file-input-button">
                    <input type="file" name="image" data-url="{url link='digitaldownload.add'}{$bEdit}/upload" accept="image/jpeg,image/png,image/gif">
                    <i class="fa fa-upload"></i>
                </div>
                <div class="error"></div>
            </div>
        </li>
    </ul>
</div>
