<?php
defined('PHPFOX') or exit('NO DICE!');
?>
<div class="table_header">
    {_p('Categories')}
</div>
<div class="table">
    <div id="manage-categories" class="dd nestable">
        {assign var='iPid' value=0}
        {template file='digitaldownload.controller.admincp.category'}
    </div>
    <div class="clearfix"></div>
</div>
