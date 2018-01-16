<div class="dd-manage-options">
    <h3 class="alert alert-info">{_p('Manage options')}</h3>
    <div class="dd-options">
        {if !$oDD.is_active}
            <h4>{_p('Digital Download Activation')} {$sPlanCurrencyId|currency_symbol}{$sActivatePrice}</h4>
            <input type="hidden" name="options[paid][activate]" value="1">
        {/if}
        {if count($aActivatedOptions)}
            <div class="dd-activated-options">
                <p><strong>{_p('Already Activated')}:</strong></p>
                <ul>
                    {foreach from=$aActivatedOptions  key=sOptionName item=sCaption}
                        <li>{$sCaption}</li>
                    {/foreach}
                </ul>
            </div>
        {/if}
        {if count($aFreeOptions)}
            <div class="dd-free-options">
                <p><strong>{_p('Available Free Options')}:</strong></p>
                <ul>
                    {foreach from=$aFreeOptions  key=sOptionName item=sCaption}
                        <li>
                            <label class="checkbox">
                                <input type="checkbox" name="options[free][{$sOptionName}]" value="1">
                                {$sCaption}
                            </label>
                        </li>
                    {/foreach}
                </ul>
            </div>
        {/if}
        {if count($aPaidOptions)}
            <div class="dd-free-options">
                <p><strong>{_p('Available Free Options')}:</strong></p>
                <ul>
                    {foreach from=$aPaidOptions  key=sOptionName item=aOption}
                        <li>
                            <label class="checkbox">
                                <input type="checkbox" name="options[paid][{$sOptionName}]" value="1">
                                {$aOption.caption}&nbsp;&nbsp;{$sPlanCurrencyId|currency_symbol}{$aOption.price}
                            </label>
                        </li>
                    {/foreach}
                </ul>
            </div>
        {/if}
    </div>
    {if !$oDD.is_active}
        <div class="alert alert-info">
            <p>{_p('Click to apply for activation Digital Download')}</p>
        </div>
    {/if}
    <input type="submit" name="options_apply" class="btn btn-primary" value="{_p('Apply')}">
</div>
