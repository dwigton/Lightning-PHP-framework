<?php
class Lightning_Module extends Lighning_Module_Base
{
    protected function initializeModules()
    {
        $this->addModule('app/base/config.php','Base_Module_Config','initialize');
        
    }
    
}