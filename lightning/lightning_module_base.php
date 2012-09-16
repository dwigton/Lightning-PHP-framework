<?php
class Lightning_Module_Base
{
    //private $_modules = array();
    public function __construct() {
        $this->initializeModules();
    }
    
    public function addModule($config_file_path, $module_class_name, $init_method)
    {
        if(file_exists($config_file_path)){
            include_once $config_file_path;
            if(class_exists($module_class_name)){
                
                $ModuleReflectionClass = new ReflectionClass($module_class_name); 

                if($ModuleReflectionClass->hasMethod($init_method)){
                    $module = new $module_class_name();
                    call_user_func(array($module,$init_method));
                }
            }
        }
    } 
}