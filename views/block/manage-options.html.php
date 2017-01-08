<div class="dd-manage-options">
    <h3 class="alert alert-info">{_p('Manage options')}</h3>
    <div class="dd-options">
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
                                <input type="checkbox" name="options[{$sOptionName}]" value="1">
                                {$sCaption}
                            </label>
                        </li>
                    {/foreach}
                </ul>
            </div>
        {/if}
        {if count($aPaidOptions)}
            <div class="dd-free-options">
                <p><strong>{_p('Available Paid Options')}:</strong></p>
                <ul>
                    {foreach from=$aPaidOptions  key=sOptionName item=aOption}
                        <li>
                            <label class="checkbox">
                                <input type="checkbox" name="options[{$sOptionName}]" value="1">
                                {$aOption.caption}&nbsp;&nbsp;{$sPlanCurrencyId|currency_symbol}{$aOption.price}
                            </label>
                        </li>
                    {/foreach}
                </ul>
            </div>
        {/if}
    </div>
    <input type="submit" name="options_apply" class="btn btn-primary" value="{_p('Apply')}">
</div>
