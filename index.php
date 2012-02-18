<?php
/* Turn on error reporting for development */

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Require all base framework files
require_once 'lightening/lightening_router_base.php';
require_once 'routes.php';
require_once 'lightening/lightening_controller_base.php';
require_once 'lightening/lightening_view_base.php';
require_once 'lightening/lightening_event.php';
require_once 'lightening/app.php';


// Set APPBASEPATH to the part of the URI that points to this Lightening application
// in the case of www.example.com/ set $APPBASEPATH = "";
// in the case where the app resides at www.example.com/somefolder/
// then set $APPBASEPATH = "/somefolder";

$APPBASEPATH = "";

// Nicely format output to browser for human readability. Not terribly useful for
// a production site as it slows down requests, but great for tutorials.
// Uncomment the following line to enable.

//Lightening_Event::addObserver('Render_Complete', 'lightening/app.php', 'APP', 'formatOutput');

// Trim 'REQUEST_URI' if index.php is not in the server's document root.

$relativepath = substr($_SERVER['REQUEST_URI'],strlen($APPBASEPATH));

$ROUTER = new lightening_router($relativepath);

require_once $ROUTER->controllerFile();

$controller_class_name = $ROUTER->controller();

$CONTROLLER = new $controller_class_name();

ob_start('afterRender');
call_user_func_array(array($CONTROLLER,$ROUTER->method()),$ROUTER->parameters());
ob_end_flush();

function afterRender($html){
    APP::setBuffer($html);
    Lightening_Event::raiseEvent('Render_Complete', array('html'=>$html));
    return APP::getBuffer();
}
