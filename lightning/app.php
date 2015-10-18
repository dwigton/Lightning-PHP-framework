<?php

/*
 * This class contains universal methods and parameters
 * 
 */
class App
{
    private static $output_buffer;
    private static $router;
    private static $data_sources = array();
    private static $theme_templates = array();
    private static $environment = 'development'; 
    
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
    
    public static function addModel($adapter_name, $source, $file_path, $model_class)
    {
        self::$data_sources[$adapter_name]->addModel($source, $file_path, $model_class);
    }
    
    public static function addCollection($adapter_name, $source, $file_path, $collection_class)
    {
        self::$data_sources[$adapter_name]->addCollection($source, $file_path, $collection_class);
    }
    
    public static function model($adapter_name, $source)
    {
        return self::$data_sources[$adapter_name]->getNewModel($source);
    }
    
    public static function collection($adapter_name, $source)
    {
        return self::$data_sources[$adapter_name]->getNewCollection($source);
    }
    
    public static function addDataAdapter($source, Lightning_Adapter $adapter)
    {
        self::$data_sources[$source] = $adapter;
    }
    
    public static function addEnvironment($name, $url)
    {
        if ($url === BASE_URL) {
            self::$environment = $name;
        }
    }
    
    public static function isEnvironment($name)
    {
        return $name === self::$environment;
    }
    
    public static function getDataAdapter($name)
    {
        return self::$data_sources[$name];
    }

    public static function replaceTemplate($old_template, $new_template)
    {
        self::$theme_templates[$old_template] = $new_template;
    }

    public static function getTemplate($file_path)
    {
        if (isset(self::$theme_templates[$file_path])) {
            return self::$theme_templates[$file_path];
        } else {
            return $file_path;
        }
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
        }
        
        fwrite($fp, "$message\n");
        fclose($fp);
    }
}
