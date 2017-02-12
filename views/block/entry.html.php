<?php

defined('PHPFOX') or exit('NO DICE!');
?>
<div id="js_dd_item_holder_{$aEntry.id}" class="dd-box-product-outer" {if $aEntry.highlighted}
     style="background-color: <?=Phpfox::getParam('cm_dd_highlighted_color', '#FFF0D1');?>"
     {/if}>
    <article itemscope itemtype="http://schema.org/Product" class="cm-dd-search-item dd-box-product">
        {if $aEntry.user_id == Phpfox::getUserId()}
            {assign var="bIsOwner" value=true}
        {else}
            {assign var="bIsOwner" value=false}
        {/if}

        {if !isset($bIsInFeed) && (isset($bShowModeration) && !empty($bShowModeration))}
            <div class="cd-dd-moderate">
                <div class="pull-left">
                    {if isset($bShowModeration) && !empty($bShowModeration)}
                    <div class="_moderator">
                        <a href="#{$aEntry.id}" class="moderate_link built"><i class="fa"></i></a>
                    </div>
                    {/if}
                </div>
                <div class="pull-right">
                    <ul class="list-inline">
                        {if  $bIsOwner || Phpfox::getUserParam('digitaldownload.can_activate_deactivate_other')}
                            {if !$aEntry.is_active}
                                <li>
                                    <a href="{url link='digitaldownload.activate'}{$aEntry.id}" class="js_dd_activate color-success" title="{_p('Activate')}" data-toggle="tooltip">
                                        <i class="fa fa-eye"></i>
                                    </a>
                                </li>
                            {else}
                                <li>
                                    <a href="{url link='digitaldownload.deactivate'}{$aEntry.id}" class="sJsConfirm js_dd_deactivate color-warning" title="{_p('Deactivate')}" data-toggle="tooltip">
                                        <i class="fa fa-eye-slash"></i>
                                    </a>
                                </li>
                            {/if}
                        {/if}

                        {if $bIsOwner || Phpfox::getUserParam('digitaldownload.can_delete_other')}
                            <li>
                                <a href="{url link='digitaldownload.delete'}{$aEntry.id}" class="sJsConfirm color-error" title="{_p('Delete')}" data-toggle="tooltip">
                                    <i class="fa fa-trash"></i>
                                </a>
                            </li>
                        {/if}

                        {if $bIsOwner ||  Phpfox::getUserParam('digitaldownload.can_edit_other')}
                            <li>
                                <a href="{url link='digitaldownload.add' dd_id=$aEntry.id}" class="color-primary" title="{_p('Edit')}" data-toggle="tooltip">
                                    <i class="fa fa-pencil"></i>
                                </a>
                            </li>
                            <li>
                                <a href="{url link='digitaldownload.add.options' dd_id=$aEntry.id}" class="color-default" title="{_p('Manage options')}" data-toggle="tooltip">
                                    <i class="fa fa-cog"></i>
                                </a>
                            </li>
                        {/if}
                    </ul>
                </div>
                <div class="clearfix"></div>
            </div>
        {/if}
        <div class="cm-dd-search-item-img dd-img-wrapper">
            <a href="{$aEntry.url}">
                {img path='core.url_pic' file=$aEntry.main_image.image_path server_id=$aEntry.main_image.server_id suffix='_120_square' itemprop='image'}
            </a>
            <div class="dd-tags">
                {if $aEntry.featured}
                     <span class="dd-label-tags">
                        <span class="dd-label label dd-label-default dd-arrowed">
                            {_p('Featured')}
                        </span>
                     </span>
                {/if}
                {if $aEntry.sponsored}
                     <span class="dd-label-tags">
                        <span class="dd-label label dd-label-primary dd-arrowed">
                            {_p('Sponsored')}
                        </span>
                     </span>
                {/if}
            </div>
        </div>
        <div class="cm-dd-search-item-content">
           <h6 itemprop="name" class="dd-title">
                <a itemprop="url" href="{$aEntry.url}" title="{$aEntry|clean}">{$aEntry|clean}</a>
            </h6>

            <div class="price" itemprop="offers" itemscope="" itemtype="http://schema.org/Offer">
                <ul>
                    {assign var='aDDPrice' value=$aEntry.aDDPrice}
                    {if empty($aDDPrice)}
                    <li>{_p('Free')}</li>
                    {else}
                    {foreach from=$aDDPrice item=aPrice}
                    <li>
                        <span itemprop="price" title="{$aPrice.caption}" data-toggle="tooltip" data-placement="right">
                            <strong>
                                {if $aPrice.price == '0.00'}
                                {_p('Free')}
                                {else}
                                {$aPrice.currency_id|currency_symbol}{$aPrice.price|number_format:2}
                                {/if}
                            </strong>
                        </span>
                    </li>
                    {/foreach}
                    {/if}
                </ul>
            </div>
            {if !isset($bIsInFeed)}
                <ul class="listing_info">
                    <li itemprop="releaseDate">{$aEntry.time_stamp|convert_time}</li>
                    {if isset($aEntry.user_name)}
                    <li>{$aEntry|user:'':'':30}</li>
                    {/if}
                </ul>
            {/if}
            <div class="category">
                {$aEntry.category}
            </div>
        </div>
    </article>
</div>

