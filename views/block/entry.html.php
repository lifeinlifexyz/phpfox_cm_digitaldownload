<?php

defined('PHPFOX') or exit('NO DICE!');
?>
<article itemscope itemtype="http://schema.org/Product">
   <div class="row">
       <div class="col-sm-3">
           {*img server_id=$aListing.server_id title=$aListing.title path='marketplace.url_image' file=$aListing.image_path suffix='_120_square' itemprop='image'*}
       </div>
       <div class="col-sm-9">
           <p>
               <a itemprop="url" href="{url link='digitaldownload'}{$aEntry.id}" title="{$aEntry|clean}">{$aEntry}</a>
           </p>
           {if $aEntry.user_id == Phpfox::getUserId()}
           <a href="{url link='digitaldownload.add'}{$aEntry.id}#detail">{_p('Edit')}</a>
           <a href="{url link='digitaldownload.delete'}{$aEntry.id}">{_p('Delete')}</a>
           {/if}
       </div>
   </div>
</article>
