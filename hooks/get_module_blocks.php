<?php
if (version_compare(Phpfox::getVersion(), '4.5.0', '<') && $this->_sModule == 'digitaldownload') {
    \Core\Route\Controller::$name = false;
}