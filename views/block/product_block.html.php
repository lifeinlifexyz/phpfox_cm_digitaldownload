<div class="dd-box-product-outer">
    <article itemscope itemtype="http://schema.org/Product" class="dd-box-product">
        <div class="dd-img-wrapper">
            <a href="{$aEntry.url}">
                {img path='core.url_pic' file= $aEntry.main_image.image_path server_id=$aEntry.main_image.server_id suffix='_200_square' itemprop='image'}
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
        <h6 class="dd-title" itemprop="name">
            <a itemprop="url" href="{$aEntry.url}" title="{$aEntry|clean}">{$aEntry}</a>
        </h6>
        <div class="price" itemprop="offers" itemscope="" itemtype="http://schema.org/Offer">
            <ul>
                {assign var='aDDPrice' value=$aEntry.aDDPrice}
                {if empty($aDDPrice)}
                <li>{_p('Free')}</li>
                {else}
                {foreach from=$aDDPrice item=aPrice}
                <li>
                                    <span itemprop="price" title="{$aPrice.caption}" data-toggle="tooltip" data-placement="top">
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
    </article>
</div>