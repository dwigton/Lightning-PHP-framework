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
    private static $collections = array();
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
    
    public static function addModel($source, $handle, $file_path, $model_class)
    {
        if( ! array_key_exists($source, self::$models)){
            self::$models[$source] = array( $handle => array(
                'file'  => $file_path,
                'class' => $model_class
            ));
        }else{
            self::$models[$source][$handle] = array(
                'file'  => $file_path,
                'class' => $model_class
            );
        }
    }
    
    public static function addCollection($source, $handle, $file_path, $model_class)
    {
        if( ! array_key_exists($source, self::$collections)){
            self::$collections[$source] = array( $handle => array(
                'file'  => $file_path,
                'class' => $model_class
            ));
        }else{
            self::$collections[$source][$handle] = array(
                'file'  => $file_path,
                'class' => $model_class
            );
        }
    }
    
    public function getModelClass($source, $handle)
    {
        if (array_key_exists($source, self::$models) && array_key_exists($handle, self::$models[$source])) {
            return self::$models[$source][$handle]['class'];
        }else{
            if(array_key_exists($source, self::$data_sources)){
                return 'Lightning_Stored_Model';
            } else {
                return 'Lightning_Model';
            }
        }
    }
    
    public function getModelClassFile($source, $handle)
    {
        if (array_key_exists($source, self::$models) && array_key_exists($handle, self::$models[$source])) {
            return self::$models[$source][$handle]['file'];
        }else{
            if(array_key_exists($source, self::$data_sources)){
                return 'lightning/stored/model.php';
            } else {
                return 'lightning/model.php';
            }
        }
    }
    
    public function getCollectionClass($source, $handle)
    {
        if (array_key_exists($source, self::$collections) && array_key_exists($handle, self::$collections[$source])) {
            return self::$collections[$source][$handle]['class'];
        }else{
            if(array_key_exists($source, self::$data_sources)){
                return 'Lightning_Stored_Collection';
            } else {
                return 'Lightning_Collection';
            }
        }
    }
    
    public function getCollectionClassFile($source, $handle)
    {
        if (array_key_exists($source, self::$collections) && array_key_exists($handle, self::$collections[$source])) {
            return self::$collections[$source][$handle]['file'];
        }else{
            if(array_key_exists($source, self::$data_sources)){
                return 'lightning/stored/model.php';
            } else {
                return 'lightning/model.php';
            }
        }
    }
    
    public static function model($source, $handle)
    {
        $model = null;
        
        if (array_key_exists($source, self::$models) && array_key_exists($handle, self::$models[$source])) {
            
            require_once self::$models[$source][$handle]['file'];
        
            if (func_num_args() > 2) {
                $reflector = new ReflectionClass(self::$models[$source][$handle]['class']);
                $model = $reflector->newInstanceArgs(array_slice(func_get_args(), 2));
            } else {
                $model = new self::$models[$source][$handle]['class']();
            }
        } else {
            if (array_key_exists($source, self::$data_sources)) {
                $model = self::getDataSource($source)->newAdapter()->getNewModel($handle);
            } else {
                $model = new Lightning_Model;
            }
        }
        
//        require_once self::getCollectionClassFile($source, $handle);
//        $model->setCollectionType(self::getCollectionClass($source, $handle));
        
        return $model;
    }
    
    public static function collection($source, $handle)
    {
        $collection = null;
        
        if (array_key_exists($source, self::$collections) && array_key_exists($handle, self::$collections[$source])) {
            
            require_once self::$collections[$source][$handle]['file'];
        
            if (func_num_args() > 2) {
                $reflector = new ReflectionClass(self::$collections[$source][$handle]['class']);
                $collection = $reflector->newInstanceArgs(array_slice(func_get_args(), 2));
            } else {
                $collection = new self::$collections[$source][$handle]['class']();
            }
        } else {
            if (array_key_exists($source, self::$data_sources)) {
                $collection = self::getDataSource($source)->newAdapter()->getNewCollection($handle);
            } else {
                $collection = new Lightning_Collection;
            }
        }
        
//        require_once self::getModelClassFile($source, $handle);
//        $collection->setItemType(self::getModelClass($source, $handle));
        
        return $collection;
    }
    
    public static function addDataSource($source, Lightning_Stored_Connection $connection)
    {
        $connection->setSource($source);
        self::$data_sources[$source] = $connection;
    }
    
    public static function getDataSource($source)
    {
        return self::$data_sources[$source];
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
