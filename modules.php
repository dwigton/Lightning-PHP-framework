<?php
class Lightning_Module_Manager extends Lightning_Module_Base
{
    protected function initializeModules()
    {
   /*
    *   Add Modules to the application witht the addModule() function
    *   
    *   addModule("module_configuration_file_path/filename.php",
    *            "name_of_configuration_class",
    *            "name_of_method");
    *   
    *   All that adding a Module does is include the designated file, instatiate
    *   an instance of the class and call its initialization function. From 
    *   there is up to the programmer to take care of any initialization tasks 
    *   they wish. Typical tasks include.
    * 
    *   Adding a controller route: App::router()->addRoute(); see Routes.php for
    *   proper use of addRoute()
    * 
    *   Adding an event observer: Lightning_Event::addObserver();
    * 
    *   Adding models to make use of Lightning's model instantiation utility:
    *   App::addModel();
    * 
    */
        
        $this->addModule('modules/base/config.php','Base_Module_Config','config');
        $this->addModule('modules/output_formatter/config.php','Output_Formatter_Module_Config','config');
        
    /* Add your modules here */
        $this->addModule('modules/stonecottage_theme/config.php', 'StoneCottage_Theme_Config', 'config');
    } 
}