<?php 
defined('PHPFOX') or exit('NO DICE!');
?>
{if isset($categories[$iPid])}
    <ol class="dd-list">
        {foreach from=$categories[$iPid] item=category}
            <li class="dd-item {if !$category.is_active}cat-not-active{/if}" data-id="{$category.category_id}">
                <div class="dd-content">
                    <div class="dd-handle">
                        {phrase var=$category.name}
                    </div>

                    <div class="pull-right actions">
                        <a href="{url link='digitaldownload.admincp.add-category.' id=$category.category_id}" class="popup blue"
                           title="{phrase var='admincp.edit'}">
                            <i class="fa fa-pencil-square"></i>
                        </a>

                        <a href="{url link='admincp.digitaldownload.categories.delete' id=$category.category_id}"
                           title="{phrase var='admincp.delete'}" class="red delete">
                            <i class="fa fa-remove"></i>
                        </a>

                        <a data-url="{url link='admincp.digitaldownload.categories.status'}"
                           href="{url link='admincp.digitaldownload.categories.status' status=0 ids[]=$category.category_id}"
                           title="{phrase var='admincp.deactivate'}"
                           {if !$category.is_active}style="display:none"{/if} class="red deactivate status">
                            <i class="fa fa-circle"></i>
                        </a>
                        <a data-url="{url link='admincp.digitaldownload.categories.status'}"
                           href="{url link='admincp.digitaldownload.categories.status' status=1 id=$category.category_id}" title="{phrase var='admincp.activate'}"
                           {if $category.is_active}style="display:none"{/if} class="activate status">
                            <i class="fa fa-circle-o"></i>
                        </a>

                        <a href="" title="{_p('Fields')}" class="green"><i class="fa fa-list"></i></a>
                </div>
                </div>

                {assign var='iPid' value=$category.category_id}
                {template file='digitaldownload.controller.admincp.category'}
            </li>
        {/foreach}
    </ol>
{/if}