<?php
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    
    require_once 'lightening/lightening_router_base.php';
    require_once 'routes.php';
    require_once 'lightening/lightening_controller_base.php';
    require_once 'lightening/lightening_view_base.php';
    $ROUTER = new lightening_router($_SERVER['REQUEST_URI']);
    
    require_once $ROUTER->controllerFile();
    $controller_class_name = $ROUTER->controller();

    $CONTROLLER = new $controller_class_name();
    call_user_func_array(array($CONTROLLER,$ROUTER->method()),$ROUTER->parameters());
?>
