<?php

/*  
 *  Add Environments
 */
    App::addEnvironment('development', 'http://dev.example.com');
    App::addEnvironment('production', 'http://www.example.com');
    
/*
 *  Turn on error reporting for development.
 */
    if (App::isEnvironment('development')) {
        error_reporting(E_ALL);
        ini_set('display_errors', 1);
    }
/*
*   Add Routes to controllers with the addRoute() function
*   
*   addRoute("/regex1/regex2/./regexn",
*            "controller/path",
*            "name_of_controller_class",
*            "name_of_method");
*   
*   The regular expressions will be put in variables referenceble as #1, #2, #3 etc..
*   All further items in the URI will be sent to the matching method as arguments. 
*   The number of arguments must be greater than or equal to the number of required 
*   parameters for the method and less
*   than or equal to the number of total parameters.
* 
*   An incorrect number of parameters, a non-existant controller class or method, 
*   or incorrect file path will result in calling a default 404 page not found error.
* 
*   The Lightning_Router class will choose the last matching route added. Thus it
*   is a good idea to start with the most general route and add more specific routes lower
*   in the list.
*
*   The following routes are an example of a single path setup where all the
*   controllers are in one place.
*/

//    App::addRoute('', 'app/controllers/index_controller.php', 'Index_Controller_Class', 'indexAction');
//    App::addRoute('.+', 'app/controllers/index_controller.php', 'Index_Controller_Class', '#1Action');
//    App::addRoute('.+', 'app/controllers/#1_controller.php', '#1\U_Controller_Class', 'indexAction');
//    App::addRoute('.+/.+', 'app/controllers/#1_controller.php', '#1\U_Controller_Class', '#2Action');
    
/*
 *  The following routes are an example of a modular setup where each module
 *  contains its own controllers.
 */
    App::addRoute('', 'modules/base/controllers/index_controller.php', 'Base_Index_Controller_Class' , 'indexAction');
    App::addRoute('.+', 'modules/base/controllers/index_controller.php', 'Base_Index_Controller_Class', '#1Action');
    App::addRoute('.+', 'modules/base/controllers/#1_controller.php', 'Base_#1\U_Controller_Class', 'indexAction');
    App::addRoute('.+', 'modules/#1/controllers/index_controller.php', '#1\U_Index_Controller_Class', 'indexAction');
    App::addRoute('.+/.+', 'modules/base/controllers/#1_controller.php', 'Base_#1\U_Controller_Class', '#2Action');
    App::addRoute('.+/.+', 'modules/#1/controllers/index_controller.php', '#1\U_Index_Controller_Class', '#2Action');
    App::addRoute('.+/.+', 'modules/#1/controllers/#2_controller.php', '#1\U_#2\U_Controller_Class', 'indexAction');
    App::addRoute('.+/.+/.+', 'modules/#1/controllers/#2_controller.php', '#1\U_#2\U_Controller_Class', '#3Action');

/*
*   Add Modules to the application with the addModule() function
*   
*   App::addModule("module_configuration_file_path/filename.php",
*            "name_of_configuration_class",
*            "name_of_method");
*   
*   All that adding a Module does is include the designated file, instatiate
*   an instance of the class and call its initialization function. From 
*   there is up to the programmer to take care of any initialization tasks 
*   they wish. Typical tasks include.
* 
*   Adding a controller route: App::addRoute(); see Routes notes above for
*   proper use of addRoute()
* 
*   Adding an event observer: Lightning_Event::addObserver();
* 
*   Adding models to make use of Lightning's model instantiation utility:
*   App::addModel();
*
*   Adding a new data source: App::addDataSource(); 
* 
*/

    App::addModule('modules/base/config.php', 'Base_Module_Config', 'config');
    App::addModule('modules/output_formatter/config.php', 'Output_Formatter_Module_Config', 'config');

/* Add your modules here */
    
/*
 *  Add Data Sources with the addDataSource() function
 * 
 *  App::addDataSource(
 *      "data_source_name", 
 *      Lightning_Stored_Adapter_Abstract $data_adapter
 *  );
 *  
 *  addDataSource() adds an initialized adapter to an internal list making that
 *  data source available throughout the application with
 * 
 *  App::getDataSource("data_source_name")
 * 
 *  By default stored models will use App::getDataSource("default") thus the
 *  application can be set to use any database or file system for its persistence
 *  layer by changing the following statement to use a different adapter.
 */
    
