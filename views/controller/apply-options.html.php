<?php
defined('PHPFOX') or exit('NO DICE!');
?>
{if $bInvoice}
    <h3>{_p('Payment Methods')}</h3>
    {module name='api.gateway.form'}
{else}
    <h3>{_p('Options you\'re buying')}</h3>
    <div class="cm-dd-view-block list">
        <form method="post" class="cd-dd dd-box-product-outer">
            <input type="hidden" name="dd_id" value="{$oDD.id}">
            <input type="hidden" name="confirm_pay" value="1">
            <input type="hidden" name="process" value="1">
            <article class="cm-dd-search-item dd-box-product row">
                <div class="col-sm-3">
                    <a href="{$oDD.url}">
                        <span class="dd-product-img"
                              style="background-image: url({img path='core.url_pic' file=$oDD.main_image.image_path server_id=$oDD.main_image.server_id suffix='_200_square' return_url=true});"
                              itemprop='image'></span>
                    </a>
                </div>
                <div class="col-sm-9">
                    <p><a href="{$oDD.url}">{$oDD}</a></p>
                    {foreach from=$aOptions key=sOptionName item=aItem}
                    <input type="hidden" name="options[paid][{$sOptionName}]" value="1">
                    <p>
                    <div class="info">
                        <div class="info_left">{$aItem.caption}</div>
                        <div class="info_right">{$sPlanCurrencyId|currency_symbol}{$aItem.price}</div>
                    </div>
                    </p>
                    {/foreach}
                    <p>
                    <div class="info">
                        <div class="info_left">
                            <strong>{_p('Total')}</strong>
                        </div>
                        <div class="info_right">
                            <strong>{$sPlanCurrencyId|currency_symbol}{$iTotalPrice}</strong>
                        </div>
                    </div>
                    </p>
                    <p>{_p('By clicking on the button below')}</p>
                    <input type="submit" value="{_p('Commit to Buy')}"  class="button btn-primary"/>
                    <p></p>
                </div>
            </article>
        </form>
    </div>

{/if}
