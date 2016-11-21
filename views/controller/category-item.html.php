<?php 
defined('PHPFOX') or exit('NO DICE!');
?>
{if isset($aCategory[$iPid])}
    <ul class="dd-list">
        {foreach from=$aCategory[$iPid] item=category}
            <li class="dd-item {if !$category.is_active}cat-not-active{/if}" data-id="{$category.category_id}">
                <a href="{$sUrl}?category_id={$category.category_id}">{phrase var=$category.name}</a>
                {assign var='iPid' value=$category.category_id}
                {template file='digitaldownload.controller.category-item'}
            </li>
        {/foreach}
    </ul>
{/if}