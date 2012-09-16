<?php

class Lightning_Event {
    
    private static $_observers = array();
    
    public static function addObserver($event_name, $file_name, $class, $function)
    {
        self::$_observers[] = array('name'=>$event_name, 'file'=>$file_name, 'class'=>$class, 'function'=>$function);
    }
    
    public static function raiseEvent($event_name, $data = array())
    {
        foreach(self::$_observers as $observer){
            if($event_name == $observer['name'] && file_exists($observer['file'])){
                include_once $observer['file'];
                if(class_exists($observer['class'])){
                    $observer_class = new $observer['class']();
                    $observer_class->$observer['function']($data);
                }
            }
        }
    }
}

?>
