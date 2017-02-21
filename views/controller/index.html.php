<?php
defined('PHPFOX') or exit('NO DICE!');
?>
{if  isset($aDDs) && count($aDDs)}
    {if !PHPFOX_IS_AJAX}
        <div class="bordered-title cm-dd-viewmode-control">
            <span class="dd-view-mode-caption">{_p('digitaldownload_view_mode_list')}</span>
            <div class="dd-view-mode-buttons">
                <button type="button" class="btn cm-dd-btn-mode list" data-mode="list"
                        title="{_p('digitaldownload_view_mode_list')}">
                    <i class="fa fa-list"></i>
                </button>
                <button type="button" class="btn cm-dd-btn-mode grid" data-mode="grid"
                        title="{_p('digitaldownload_view_mode_grid')}">
                    <i class="fa fa-th"></i>
                </button>
            </div>
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
    {if (Phpfox::isAdmin() || !empty($bShowModeration)) && empty($bIsNoModerate)}
        {moderation}
    {/if}
{elseif !PHPFOX_IS_AJAX}
{_p('No digital download fond')}
{/if}