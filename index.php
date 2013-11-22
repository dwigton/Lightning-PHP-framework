<?php
// Include the App class.
require_once 'lightning/app.php';
// Adding a function to find all the base lightnining files.
spl_autoload_register('loadLightning');

define('ROOT_PATH', substr($_SERVER['SCRIPT_FILENAME'], 0, -10));
define('PROJECT_DIR', rtrim(strstr($_SERVER['PHP_SELF'], 'index.php', true), '/'));
define('BASE_URL', 'http://'.$_SERVER['HTTP_HOST'].PROJECT_DIR);

// Trim 'REQUEST_URI' if index.php is not in the server's document root.
define('URI', current(explode('?', substr($_SERVER['REQUEST_URI'],strlen(PROJECT_DIR)), 2)));

App::initRouter(URI);

// Load the project config.
require_once 'config.php';

Lightning_Event::raiseEvent('Before_Controller_Load');

require_once App::router()->controllerFile();
$controller_class_name = App::router()->controller();
$controller = new $controller_class_name();

ob_start('afterRender');
call_user_func_array(array($controller, App::router()->method()), App::router()->parameters());
ob_end_flush();

function afterRender($html)
{
    App::setBuffer($html);
    Lightning_Event::raiseEvent('Render_Complete', array('html' => $html));
    return App::getBuffer();
}

function loadLightning($class_name)
{
    $path = ROOT_PATH."/".strtolower(str_replace('_', '/', $class_name)).".php";
    if (file_exists($path)) {
        include_once $path;
    }
}
