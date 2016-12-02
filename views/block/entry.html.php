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
           {$aEntry.category_id}
       </div>
       <h2 itemprop="name">
           <a itemprop="url" href="{$aEntry.url}" title="{$aEntry|clean}">{$aEntry}</a>
       </h2>

       <div class="price" itemprop="offers" itemscope="" itemtype="http://schema.org/Offer">
		<span itemprop="price">{$aEntry.price}</span>
       </div>

       <ul class="listing_info">
           <li itemprop="releaseDate">{$aEntry.time_stamp|convert_time}</li>
           {if isset($aEntry.user_name)}
           <li>{$aEntry|user:'':'':30}</li>
           {/if}
       </ul>

       {if $aEntry.user_id == Phpfox::getUserId()}
       <a href="{url link='digitaldownload.add'}{$aEntry.id}#detail">{_p('Edit')}</a>
       <a href="{url link='digitaldownload.delete'}{$aEntry.id}">{_p('Delete')}</a>
       {/if}
   </div>
    <div class="clearfix"></div>
</article>
