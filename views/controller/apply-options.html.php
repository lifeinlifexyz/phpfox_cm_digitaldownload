<?php
defined('PHPFOX') or exit('NO DICE!');
?>
{if $bInvoice}
    <h3>{_p('Payment Methods')}</h3>
    {module name='api.gateway.form'}
{else}
    <h3>{_p('Options you\'re buying')}</h3>
    <form method="post">
        <input type="hidden" name="dd_id" value="{$oDD.id}">
        <input type="hidden" name="process" value="1">
        <article class="cm-dd-search-item">
            <div class="cm-dd-search-item-img">
                <a href="{$oDD.url}">
                    {img path='core.url_pic' file='digitaldownload/'.$oDD.main_image.image_path server_id=$oDD.main_image.server_id suffix='_120_square' itemprop='image'}
                </a>
            </div>
            <div class="cm-dd-search-item-content">
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
            </div>
        </article>
        <p>{_p('By clicking on the button below')}</p>
        <input type="submit" value="{_p('Commit to Buy')}"  class="button btn-primary"/>
    </form>
{/if}
