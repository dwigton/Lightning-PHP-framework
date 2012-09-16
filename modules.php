<?php
class Lightning_Module_Manager extends Lightning_Module_Base
{
    protected function initializeModules()
    {
        $this->addModule('modules/base/config.php','Base_Module_Config','config');
        $this->addModule('modules/output_formatter/config.php','Output_Formatter_Module_Config','config'); 
    } 
}