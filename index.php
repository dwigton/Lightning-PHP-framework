<?php
/* Turn on error reporting for development */

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Require all base framework files.
require_once 'lightning/lightning_router_base.php';
require_once 'routes.php';
require_once 'lightning/lightning_view_base.php';
require_once 'lightning/lightning_event.php';
require_once 'lightning/app.php';


// Set PROJECT_DIR to the part of the URI that points to this Lightning application
// in the case of www.example.com/ set PROJECT_DIR = "/";
// in the case where the app resides at www.example.com/somefolder/
// then set PROJECT_DIR = "/somefolder/";

define('ROOT_PATH', $_SERVER['DOCUMENT_ROOT']);
define('PROJECT_DIR', '/');
define('BASE_URL', 'http://'.$_SERVER['HTTP_HOST'].PROJECT_DIR);

// Trim 'REQUEST_URI' if index.php is not in the server's document root.
define('URI', substr($_SERVER['REQUEST_URI'],strlen(PROJECT_DIR)-1));

// Nicely format output to browser for human readability. Not terribly useful for
// a production site as it slows down requests, but great for tutorials.
// Uncomment the following line to enable.
Lightning_Event::addObserver('Render_Complete', 'lightning/app.php', 'APP', 'formatOutput');

App::initRouter(URI);

$router = new lightning_router(URI);

require_once $router->controllerFile();

$controller_class_name = $router->controller();

$controller = new $controller_class_name();

ob_start('afterRender');
call_user_func_array(array($controller,$router->method()),$router->parameters());
ob_end_flush();

function afterRender($html)
{
    App::setBuffer($html);
    Lightning_Event::raiseEvent('Render_Complete', array('html'=>$html));
    return App::getBuffer();
}
