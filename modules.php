<?php
class Lightning_Module_Manager extends Lightning_Module_Base
{
    protected function initializeModules()
    {
        $this->addModule('app/base/config.php','Base_Module_Config','config');
        App::log('Made It this Far', 'debug.log');
    } 
}