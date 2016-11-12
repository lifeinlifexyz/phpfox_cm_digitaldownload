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
                        <a href="{url link='digitaldownload.admincp.add-category.' id=$category.category_id}" class="popup" title="{phrase var='admincp.edit'}"><i class="fa fa-pencil-square"></i></a>
                        <a href="" title="{phrase var='admincp.delete'}"><i class="fa fa-remove"></i></a>
                        <a href="" title="{_p('Fields')}"><i class="fa fa-list"></i></a>
                </div>
                </div>

                {assign var='iPid' value=$category.category_id}
                {template file='digitaldownload.controller.admincp.category'}
            </li>
        {/foreach}
    </ol>
{/if}