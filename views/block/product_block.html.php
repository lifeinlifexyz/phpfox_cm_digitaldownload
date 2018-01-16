<div class="dd-box-product-outer cd-dd">
    <article itemscope itemtype="http://schema.org/Product" class="dd-box-product">

        <div class="dd-product-header">
            <p itemprop="name" class="dd-product-title cd-dd-upper">
                <a itemprop="url" href="{$aEntry.url}" title="{$aEntry|clean}">{$aEntry|clean}</a>
            </p>
            {if isset($aEntry.short_description) && !empty($aEntry.short_description)}
            <div class="dd-product-description">{$aEntry.short_description}</div>
            {/if}
        </div>

        <div class="dd-img-wrapper">
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
            <div class="row">
                <div class="col-xs-6">
                    <div class="dd-product-category">
                        {$aEntry.category}
                    </div>
                </div>
                <div class="col-xs-6 text-right cd-dd-upper">
                    {if isset($aEntry.user_name)}
                    {$aEntry|user:'':'':30}
                    {/if}
                </div>
            </div>
            <div class="dd-product-price-wrap" itemprop="offers" itemscope="" itemtype="http://schema.org/Offer">
                <div class="row">
                    {assign var='aDDPrice' value=$aEntry.aDDPrice}
                    {if empty($aDDPrice)}
                    {_p('Free')}
                    {else}
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
                    {/if}
                </div>
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