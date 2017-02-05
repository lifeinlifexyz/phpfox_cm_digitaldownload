<?php

defined('PHPFOX') or exit('NO DICE!');
?>
{if isset($aDDs) && count($aDDs)}
    <div class="featured-block">
        {for $i = 0; $i < count($aDDs); $i++}
            {assign var="aEntry" value=$aDDs[$i]}
            {template file='digitaldownload.block.product_block'}
        {/for}
    </div>
{/if}
