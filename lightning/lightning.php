<?php

// Include the App class.
require_once 'app.php';
// Adding a function to find all the base lightnining files.
spl_autoload_register('loadLightning');

$stack = debug_backtrace();

define('ROOT_PATH', substr($stack[count($stack) - 1]['file'], 0, -10));
define('PROJECT_DIR', rtrim(strstr($_SERVER['PHP_SELF'], 'index.php', true), '/'));
define('BASE_URL', 'http://'.$_SERVER['HTTP_HOST'].PROJECT_DIR);

// Trim 'REQUEST_URI' if index.php is not in the server's document root.
define('URI', current(explode('?', substr($_SERVER['REQUEST_URI'],strlen(PROJECT_DIR)), 2)));

App::addModule('lightning', dirname(__FILE__), '', 'Lightning');
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
    $namespace = '';
    $file_path = '';

    if (count($namespaces) > 1) {
        $namespace = array_shift($namespaces);
        $file_path = strtolower(str_replace('_', '/', end($namespaces))).".php";
    } else {
        $file_path = strtolower(str_replace('_', '/', $class_name)).".php";
    }

    foreach (App::getModules() as $module) {
        //echo "<p>".$module['directory']."/$file_path"."</p>";
        if ($module['namespace'] == $namespace && is_file($module['directory']."/$file_path")) {
           include_once $module['directory']."/$file_path";
        } 
    }
}
