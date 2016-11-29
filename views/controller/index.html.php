<?php
defined('PHPFOX') or exit('NO DICE!');
?>
{if  isset($aDDs) && count($aDDs)}
    {for $i = 0; $i < $iCount; $i++}
        {assign var="aEntry" value=$aDDs[$i]}
        {module name='digitaldownload.entry'}
    {/for}
{pager}
{elseif !PHPFOX_IS_AJAX}
{_p('No digital download fond')}
{/if}