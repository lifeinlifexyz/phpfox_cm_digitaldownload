<?php
defined('PHPFOX') or exit('NO DICE!');
?>
<div class="dd-select-plan cd-dd">
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
                        <div class="row dd-plan-th">
                            <div class="col-xs-6 text-left">
                                {_p('Option')}
                            </div>
                            <div class="col-xs-6 text-right">
                                {_p('Value')}
                            </div>
                        </div>
                    </div>
                    <div class="dd-plan-option row">
                        <span class="dd-plan-option-caption">{_p('Digital Download Activation')}</span>
                        <span class="dd-plan-option-value"><strong>{$aItem.price}</strong></span>
                    </div>
                    <div class="dd-plan-option">
                        <span class="dd-plan-option-caption">
                            {_p('Life time(in day)')}
                        </span>
                        <span class="dd-plan-option-value"><strong>{$aItem.life_time}</strong></span>
                    </div>
                    <div class="dd-extra-options">
                        {$aItem}
                    </div>
                    <div class="dd-plan-option last">
                        <span class="dd-plan-option-caption">{_p('Allowed pictures count')}</span>
                        <span class="dd-plan-option-value"><strong>{$aItem.allowed_count_pictures}</strong></span>
                    </div>
                    <div class="dd-plan-footer">
                        <a class="btn dd-btn-buy-plan" href="{$sUrl}&plan_id={$aItem.plan_id}">{_p('Buy plan now')}</a>
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
