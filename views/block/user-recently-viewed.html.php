<?php

defined('PHPFOX') or exit('NO DICE!');
?>
{if isset($aDDs) && count($aDDs)}
<div class="block">
    <br>
    <div class="similar-block">
        <div class="bordered-title">
            <span>{_p('Recently viewed')}</span>
        </div>
        <div class="owl-carousel owl-theme owl-navs-top-offset" data-def="1" data-small="2" data-large="3">
            {for $i = 0; $i < count($aDDs); $i++}
                {assign var="aEntry" value=$aDDs[$i]}
                <div class="item">
                    {template file='digitaldownload.block.product_block'}
                </div>
            {/for}
        </div>

    </div>
</div>
{/if}
