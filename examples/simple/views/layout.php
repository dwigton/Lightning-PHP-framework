<?php
class Layout extends Lightning\View
{
    public function __construct()
    {
        parent::__construct('templates/layout.php');
        $this->addItem('css', BASE_URL.'/media/css/reset.css');
        $this->addItem('css', BASE_URL.'/media/css/styles.css');
        if (App::isEnvironment('production')) {
            // This is a good place to enable analytics or add other scripts
            // that should only run in one environment.
        }
    }
}
