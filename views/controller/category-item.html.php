<?php 
defined('PHPFOX') or exit('NO DICE!');
?>
{if isset($aCategory[$iPid])}
    <ul class="dd-list">
        {foreach from=$aCategory[$iPid] item=category}
            <li>
                {if $category.parent_id != 0}
                    <span class="dd-angle">&#9492;</span>
                {/if}
                <a href="{$sUrl}?category_id={$category.category_id}">{phrase var=$category.name}</a>
                {assign var='iPid' value=$category.category_id}
                {template file='digitaldownload.controller.category-item'}
            </li>
        {/foreach}
    </ul>
{/if}