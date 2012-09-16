<?php
class Home_Page_View extends Lightning_View
{
    public function __construct()
    {
        $this->setTemplateFile('app/templates/home_page_template.php');
        $this->addScript('media/script/jquery.js');
        $this->addScript('media/script/lightning-test.js');
    }
}
