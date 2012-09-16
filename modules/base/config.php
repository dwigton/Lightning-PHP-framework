<?php
class Base_Module_Config
{
    public function config()
    {
        App::router()->addRoute('', 'modules/base/controllers/index_controller.php', 'Base_Index_Controller_Class' , 'indexAction');
        App::router()->addRoute('[a-z]+', 'modules/base/modules/controllers/index_controller.php', 'Base_Index_Controller_Class', '#1Action');
        App::router()->addRoute('[a-z]+', 'modules/base/modules/controllers/#1_controller.php', 'Base_#1\U_Controller_Class', 'indexAction');
        App::router()->addRoute('[a-z]+', 'modules/#1/controllers/index_controller.php', '#1\U_Index_Controller_Class', 'indexAction');
        App::router()->addRoute('[a-z]+/[a-z]+', 'modules/base/modules/controllers/#1_controller.php', 'Base_#1\U_Controller_Class', '#2Action');
        App::router()->addRoute('[a-z]+/[a-z]+', 'modules/#1/controllers/index_controller.php', '#1\U_Index_Controller_Class', '#2Action');
        App::router()->addRoute('[a-z]+/[a-z]+', 'modules/#1/controllers/#2_controller.php', '#1\U_#2\U_Controller_Class', 'indexAction');
        App::router()->addRoute('[a-z]+/[a-z]+/[a-z]+', 'modules/#1/controllers/#2_controller.php', '#1\U_#2\U_Controller_Class', '#3Action');
    }
}