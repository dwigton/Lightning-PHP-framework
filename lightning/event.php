<?php

class Lightning_Event
{
    
    private static $observers = array();
    
    public static function addObserver($event_name, $file_name, $class, $function, $stop_processing = false)
    {
        self::$observers[] = array(
            'name' => $event_name,
            'file' => $file_name,
            'class' => $class,
            'function' => $function,
            'stop' => $stop_processing
        );
    }
    
    public static function raiseEvent($event_name, $data = array())
    {
        foreach (self::$observers as $observer) {
            if ($event_name == $observer['name'] && file_exists($observer['file'])) {
                include_once $observer['file'];
                if (class_exists($observer['class'])) {
                    $observer_class = new $observer['class']();
                    $observer_class->$observer['function']($data);
                    if ($observer['stop']) {
                        return;
                    }
                }
            }
        }
    }
}
