<?php

// Include the App class.
require_once 'app.php';
// Adding a function to find all the base lightnining files.
spl_autoload_register('loadLightning');

$stack = debug_backtrace();

define('LIGHTNING_DIR',dirname(__FILE__));
define('ROOT_PATH', substr($stack[count($stack) - 1]['file'], 0, -10));
define('PROJECT_DIR', rtrim(strstr($_SERVER['PHP_SELF'], 'index.php', true), '/'));
define('BASE_URL', 'http://'.$_SERVER['HTTP_HOST'].PROJECT_DIR);

// Trim 'REQUEST_URI' if index.php is not in the server's document root.
define('URI', current(explode('?', substr($_SERVER['REQUEST_URI'],strlen(PROJECT_DIR)), 2)));

App::initRouter(URI);

// Load the project config.
require_once ROOT_PATH.'/config.php';

Lightning\Event::raiseEvent('Before_Controller_Load');

require_once App::router()->controllerFile();
$controller_class_name = App::router()->controller();
$controller = new $controller_class_name();

ob_start('afterRender');
call_user_func_array(array($controller, App::router()->method()), App::router()->parameters());
ob_end_flush();

function afterRender($html)
{
    App::setBuffer($html);
    Lightning\Event::raiseEvent('Render_Complete', array('html' => $html));
    return App::getBuffer();
}

function loadLightning($class_name)
{
    $namespaces = explode('\\',$class_name);
    if (array_shift($namespaces) == 'Lightning') {
        $path = LIGHTNING_DIR."/".strtolower(str_replace('_', '/', end($namespaces))).".php";
        if (file_exists($path)) {
            include_once $path;
        }
    }
}
