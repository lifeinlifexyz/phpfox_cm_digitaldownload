<?php
defined('PHPFOX') or exit('NO DICE!');
?>
{if  isset($aDDs) && count($aDDs)}
    {if !PHPFOX_IS_AJAX}
        <div class="cm-dd-viewmode-control text-right">
            <button type="button" class="btn btn-default list" data-mode="list">
                <i class="fa fa-list"></i>
                {_p('List')}
            </button>
            <button type="button" class="btn btn-default grid" data-mode="grid">
                <i class="fa fa-th"></i>
                {_p('Grid')}
            </button>
        </div>
    {/if}
    <div class="cm-dd-view-block">
        {for $i = 0; $i < count($aDDs); $i++}
            {assign var="aEntry" value=$aDDs[$i]}
            {module name='digitaldownload.entry'}
        {/for}
    </div>

    {literal}
    <script type="text/javascript">
        $Behavior.initViewMode = function(){
            init_view_mode('dd_view_mode');
        }
    </script>
    {/literal}
{pager}
{elseif !PHPFOX_IS_AJAX}
{_p('No digital download fond')}
{/if}