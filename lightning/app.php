<?php

/*
 * This class contains universal methods and parameters
 * 
 */
class App
{
    private static $output_buffer;
    private static $router;
    private static $models = array();
    private static $data_sources = array();
    
    public static function getBuffer()
    {
        return self::$output_buffer;
    }
    
    public static function setBuffer($buffer)
    {
        self::$output_buffer = $buffer;
    }
    
    public static function router()
    {
        return self::$router;
    }
    
    public static function initRouter($uri, $router_class = 'Lightning_Router')
    {
        self::$router = new $router_class($uri);
    }
    
    public static function addRoute($pattern, $controller_route, $controller_class_name, $function_name)
    {
        self::$router->addRoute($pattern, $controller_route, $controller_class_name, $function_name);
    }
    
    public static function addModule($config_file_path, $module_class_name, $init_method)
    {
        if (file_exists($config_file_path)) {
            include_once $config_file_path;
            if (class_exists($module_class_name)) {

                $ModuleReflectionClass = new ReflectionClass($module_class_name);

                if ($ModuleReflectionClass->hasMethod($init_method)) {
                    $module = new $module_class_name();
                    call_user_func(array($module,$init_method));
                }
            }
        }
    }
    
    public static function addModel($handle, $file_path, $model_class)
    {
        self::$models[$handle] = array(
            'file'  => $file_path,
            'class' => $model_class
        );
    }
    
    public static function getModel($handle)
    {
        require_once self::$models[$handle]['file'];
        
        if (func_num_args() > 1) {
            $reflector = new ReflectionClass(self::$models[$handle]['class']);
            return $reflector->newInstanceArgs(array_slice(func_get_args(), 1));
        } else {
            return new self::$models[$handle]['class']();
        }
    }
    
    public static function addDataSource($handle, Lightning_Stored_Adapter_Abstract $adapter)
    {
        self::$data_sources[$handle] = $adapter;
    }
    
    public static function getDataSource($handle)
    {
        return self::$data_sources[$handle];
    }
    
    public static function log($message, $log_file = 'application.log')
    {
        if (!file_exists(ROOT_PATH."/var/log")) {
            mkdir(ROOT_PATH."/var/log");
        }
        $fp = fopen(ROOT_PATH."/var/log/$log_file", 'a');
        
        if (!is_string($message)) {
            ob_start();
            var_dump($message);
            $message = ob_get_clean();
            //$message = var_export($message, true);
        }
        
        fwrite($fp, "$message\n");
        fclose($fp);
    }
}
