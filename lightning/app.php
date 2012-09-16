<?php
/*
 * This class contains universal methods and parameters
 * 
 */
class App
{
    private static $_output_buffer;
    private static $_router;
    private static $_models = array();
    
    public static function getBuffer()
    {
        return self::$_output_buffer;
    }
    
    public static function setBuffer($buffer)
    {
        self::$_output_buffer = $buffer;
    }
    
    public static function router()
    {
        return self::$_router;
    }
    
    public static function initRouter($uri, $router_class = 'Lightning_Router')
    {
        self::$_router = new $router_class($uri);
    }
    
    public static function addModel($handle, $file_path, $model_class)
    {
        self::$_models[$handle] = array(
            'file'  => $file_path,
            'class' => $model_class
        );
    }
    
    public static function getModel($handle)
    {
        require_once self::$_models[$handle]['file'];
        
        if(func_num_args() > 1){
            $reflector = new ReflectionClass('class');
            return $reflector->newInstanceArgs(array_slice(func_get_args(), 1));
        }else{
            return new self::$_models[$handle]['class']();
        }
    }
    
    public static function log($message, $log_file = 'application.log')
    {
        if(!file_exists(ROOT_PATH."/var/log")){
            mkdir(ROOT_PATH."/var/log");
        }
        $fp = fopen(ROOT_PATH."/var/log/$log_file", 'a');
        fwrite($fp, "$message\n");
        fclose($fp);
    }
}
