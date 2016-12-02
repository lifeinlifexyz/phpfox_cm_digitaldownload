<?php

defined('PHPFOX') or exit('NO DICE!');
?>
<article itemscope itemtype="http://schema.org/Product">
   <div class="row">
       <div class="col-sm-3">
            {img path='core.url_pic' file='digitaldownload/'.$aEntry.main_image.image_path server_id=$aEntry.main_image.server_id suffix='_120_square' itemprop='image'}
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
