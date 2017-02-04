<?php

defined('PHPFOX') or exit('NO DICE!');
?>

<article itemscope itemtype="http://schema.org/Product" class="cm-dd-search-item">
   <div class="cm-dd-search-item-img">
       <a href="{$aEntry.url}">
         {img path='core.url_pic' file='digitaldownload/'.$aEntry.main_image.image_path server_id=$aEntry.main_image.server_id suffix='_120_square' itemprop='image'}
       </a>
   </div>
   <div class="cm-dd-search-item-content">
       <div class="category">
           {$aEntry.category}
       </div>
       <h2 itemprop="name">
           <a itemprop="url" href="{$aEntry.url}" title="{$aEntry|clean}">{$aEntry}</a>
       </h2>

       <div class="price" itemprop="offers" itemscope="" itemtype="http://schema.org/Offer">
       </div>
        {if !isset($bIsInFeed)}
           <ul class="listing_info">
               <li itemprop="releaseDate">{$aEntry.time_stamp|convert_time}</li>
               {if isset($aEntry.user_name)}
               <li>{$aEntry|user:'':'':30}</li>
               {/if}
           </ul>
       {/if}
       <ul>
           {assign var='aDDPrice' value=$aEntry.aDDPrice}
           {if empty($aDDPrice)}
            <li>{_p('Free')}</li>
           {else}
                {foreach from=$aDDPrice item=aPrice}
                    <li>
                        <span class="price-title">
                            {$aPrice.caption}
                        </span>
                        <span itemprop="price">
                            {if $aPrice.price == '0.00'}
                                {_p('Free')}
                            {else}
                                {$aPrice.currency_id|currency_symbol}{$aPrice.price|number_format:2}
                            {/if}
                        </span>
                    </li>
                {/foreach}
           {/if}
       </ul>

       {if $aEntry.user_id == Phpfox::getUserId()}
       <a href="{url link='digitaldownload.add' dd_id=$aEntry.id}">{_p('Edit')}</a>
       <a href="{url link='digitaldownload.delete'}{$aEntry.id}">{_p('Delete')}</a>
       {/if}
   </div>
    <div class="clearfix"></div>
</article>
