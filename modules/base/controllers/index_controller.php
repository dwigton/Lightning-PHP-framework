<?php
class Base_Index_Controller_Class
{
    
    public function indexAction($game = 'default')
    {
        $page = Lightning_View::newExtendedView(
                    'modules/base/views/standard_page.php',
                    'Standard_Page_View'
                )
                ->addNewChild(
                    'content', 
                    'modules/base/templates/home.php'
                )
                ->setVar('title', 'Lightning Demo Application')
                ->render();
    }
}
