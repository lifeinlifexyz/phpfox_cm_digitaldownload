<?php

defined('PHPFOX') or exit('NO DICE!');
?>
<div id="js_dd_item_holder_{$aEntry.id}" class="cd-dd dd-box-product-outer" {if $aEntry.highlighted}
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
                                    <a href="{url link='digitaldownload.activate'}{$aEntry.id}" class="js_dd_activate color-success">
                                        <i class="fa fa-eye" title="{_p('Activate')}" data-toggle="tooltip"></i>
                                    </a>
                                </li>
                            {else}
                                <li>
                                    <a href="{url link='digitaldownload.deactivate'}{$aEntry.id}" class="sJsConfirm js_dd_deactivate color-warning">
                                        <i class="fa fa-eye-slash" title="{_p('Deactivate')}" data-toggle="tooltip"></i>
                                    </a>
                                </li>
                            {/if}
                        {/if}

                        {if $bIsOwner || Phpfox::getUserParam('digitaldownload.can_delete_other')}
                            <li>
                                <a href="{url link='digitaldownload.delete'}{$aEntry.id}" class="sJsConfirm color-error">
                                    <i class="fa fa-trash" title="{_p('Delete')}" data-toggle="tooltip"></i>
                                </a>
                            </li>
                        {/if}

                        {if $bIsOwner ||  Phpfox::getUserParam('digitaldownload.can_edit_other')}
                            <li>
                                <a href="{url link='digitaldownload.add' dd_id=$aEntry.id}" class="color-primary">
                                    <i class="fa fa-pencil" title="{_p('Edit')}" data-toggle="tooltip"></i>
                                </a>
                            </li>
                            <li>
                                <a href="{url link='digitaldownload.add.options' dd_id=$aEntry.id tab='options'}" class="color-default">
                                    <i class="fa fa-cog" title="{_p('Manage options')}" data-toggle="tooltip"></i>
                                </a>
                            </li>
                        {/if}
                    </ul>
                </div>
                <div class="clearfix"></div>
            </div>
        {/if}
        <div class="dd-product-header">
            <div class="visible-list dd-product-owner-list text-right">
                <span class="dd-product-date" itemprop="releaseDate">{$aEntry.time_stamp|convert_time}</span>
                {if !isset($bIsInFeed) && isset($aEntry.user_name)}
                    <span class="cd-dd-upper">
                        {$aEntry|user:'':'':30}
                    </span>
                {/if}
            </div>

            <p itemprop="name" class="dd-product-title cd-dd-upper">
                <a itemprop="url" href="{$aEntry.url}" title="{$aEntry|clean}">{$aEntry|clean}</a>
            </p>
            {if isset($aEntry.short_description) && !empty($aEntry.short_description)}
                <div class="dd-product-description">{$aEntry.short_description}</div>
            {/if}
        </div>
        <div class="cm-dd-search-item-img dd-img-wrapper">
            <a href="{$aEntry.url}">
                <span class="dd-product-img"
                style="background-image: url({img path='core.url_pic' file=$aEntry.main_image.image_path server_id=$aEntry.main_image.server_id suffix='_400' return_url=true});"
                      itemprop='image'></span>
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

            <div class="row hidden-list">
                <div class="col-xs-6">
                    <div class="dd-product-category">
                        {$aEntry.category}
                    </div>
                </div>
                <div class="col-xs-6 text-right cd-dd-upper hidden-list">
                    {if !isset($bIsInFeed) && isset($aEntry.user_name)}
                    {$aEntry|user:'':'':30}
                    {/if}
                </div>
            </div>

            <div class="dd-product-price-wrap" itemprop="offers" itemscope="" itemtype="http://schema.org/Offer">
                    {assign var='aDDPrice' value=$aEntry.aDDPrice}
                    {if empty($aDDPrice)}
                        {_p('Free')}
                    {else}
                        <div class="row">
                            {foreach from=$aDDPrice item=aPrice}
                                <div class="col-sm-6" itemprop="price">
                                    <span class="dd-product-price-caption">
                                        {$aPrice.caption}:&nbsp;
                                    </span>
                                    <span class="dd-product-price">
                                        {if $aPrice.price == '0.00'}
                                            {_p('Free')}
                                        {else}
                                            {$aPrice.currency_id|currency_symbol}{$aPrice.price|number_format:2}
                                        {/if}
                                    </span>
                                </div>
                            {/foreach}
                        </div>
                    {/if}
            </div>

            <div class="dd-product-category visible-list">
                {$aEntry.category}
            </div>

            <div class="row">
                <div class="col-xs-6">
                    {$aEntry.rating}
                </div>
                <div class="col-xs-6 text-right dd-product-order cd-dd-upper">
                    {$aEntry.total_download | int} - {_p('Orders')}
                </div>
            </div>
        </div>
    </article>
</div>

