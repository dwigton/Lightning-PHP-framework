<?php
class Index_Controller_Class
{
    
    public function indexAction()
    {
        $page = Lightning_View::newExtendedView('app/views/standard_page_view.php', 'Standard_Page_view')
                ->addNewExtendedChild('content', 'app/views/home_page_view.php', 'Home_Page_View')
                ->setVar('title', 'Home Page')
                ->render();
    }
    
    public function simpleAction()
    {
        $page = new Lightning_View('app/templates/standard_page_template.php');
        $page->addNewChild('content', 'app/templates/home_page_template.php')
                ->addCss('media/css/reset.css')
                ->addCss('media/css/styles.css')
                ->setVar('title', 'Simple Page')
                ->render();
    }
}
?>
