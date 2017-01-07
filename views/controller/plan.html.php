<?php
defined('PHPFOX') or exit('NO DICE!');
?>
<div class="dd-select-plan">
    <div class="row">
        <?php
            $index = 0;
        ?>
        {for $i = 0; $i < count($aPlans); $i++}
            {assign var="aItem" value=$aPlans[$i]}
            <div class="col-sm-2 col-md-4">
                <div class="item dd-plan-item">
                    <div class="dd-plan-header">
                        <div class="title">{$aItem.name}</div>
                    </div>

                    <p>{_p('Digital Download Activation')}: <strong>{$aItem.price}</strong></p>
                    <p>{_p('Life time(in day)')}: <strong>{$aItem.life_time}</strong></p>
                    <div class="dd-extra-options">
                        {$aItem}
                    </div>
                    <p>{_p('Allowed pictures count')}: <strong>{$aItem.allowed_count_pictures}</strong></p>
                    <div class="dd-plan-footer">
                        <a class="btn btn-default" href="{$sUrl}&plan_id={$aItem.plan_id}">{_p('Select')} >></a>
                    </div>
                </div>
            </div>
            <?php
                $index++;
                if ($index % 3 == 0):
            ?>
                <div class="clearfix visible-md visible-lg"></div>
            <?php endif;
                if ($index % 2 == 0):
            ?>
                <div class="clearfix visible-sm"></div>
            <?php endif;?>
        {/for}
    </div>
</div>
