<?php

defined('PHPFOX') or exit('NO DICE!');
?>
{if isset($aDDs) && count($aDDs)}
<div class="block">
    <br>
    <div class="most-viewed-block">
        <div class="bordered-title">
            <span>
                <a href="{url link='digitaldownload' sort="most-talked"}">
                    {_p('Most discused')}
                    <i class="fa fa-chevron-circle-right"></i>
                </a>
            </span>
        </div>
        <div class="owl-carousel owl-theme owl-navs-top-offset" data-def="1" data-small="1" data-large="1">
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
