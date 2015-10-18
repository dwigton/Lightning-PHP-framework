<?php
class Standard_Page_View extends Lightning_View
{
    public function __construct()
    {
        parent::__construct('modules/base/templates/standard_page.php');
        $this->addItem('css', BASE_URL.'/modules/base/media/css/reset.css');
        $this->addItem('css', BASE_URL.'/modules/base/media/css/styles.css');
        if (App::isEnvironment('production')) {
            // This is a good place to enable analytics or add other scripts
            // that should only run in one environment.
        }
    }
}
