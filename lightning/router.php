<?php
class Lightning_Router
{
    private $controllerFile = 'lightning/error_controller.php';
    private $controller = 'Lightning_Error_Controller';
    private $method = 'notFound';
    private $parameters = array();
    private $uri;
    
    public function __construct($uri)
    {
        $this->uri = $paths = explode("/", trim($uri, " /"));
    }
    
    public function addRoute($pattern, $controller_route, $controller_class_name, $function_name)
    {
        $route_patterns     = explode("/", trim($pattern, " /"));
        $uri                = $this->uri;
        $parameters = array();
        
        //Parameters are stripped off the uri
        if (count($uri) > count($route_patterns)) {
            $parameters = array_slice($uri, count($route_patterns));
            $uri = array_slice($uri, 0, count($route_patterns));
        }
        
        //Return if the uri and patterns are differing lengths making a match impossible
        if (count($uri) != count($route_patterns)) {
            return;
        }
        
        //Return if any of the sub-expressions fail to match the corrosponding bit in the uri
        foreach ($route_patterns as $index => $pattern) {
            if (!preg_match('/^'.$pattern.'$/', $uri[$index])) {
                return;
            }
        }
        
        //If program reaches this point the uri matches the pattern. Set Variables.
        
        $index = count($uri);
        foreach (array_reverse($uri) as $token) {
            // Matching is done in reverse to ensure that #10 is never over-written by #1
            // The pattern #1\U is changed to ucfirst and #1\L is changed to lowercase.
            
            $controller_route = preg_replace("/\#$index\Q\U\E/", ucfirst($token), $controller_route);
            $controller_route = preg_replace("/\#$index\Q\L\E/", strtolower($token), $controller_route);
            $controller_route = preg_replace("/\#$index/", $token, $controller_route);
            
            $controller_class_name = preg_replace("/\#$index\Q\U\E/", ucfirst($token), $controller_class_name);
            $controller_class_name = preg_replace("/\#$index\Q\L\E/", strtolower($token), $controller_class_name);
            $controller_class_name = preg_replace("/\#$index/", $token, $controller_class_name);
            
            $function_name = preg_replace("/\#$index\Q\U\E/", ucfirst($token), $function_name);
            $function_name = preg_replace("/\#$index\Q\L\E/", strtolower($token), $function_name);
            $function_name = preg_replace("/\#$index/", $token, $function_name);
            
            $index--;
        }
        
        if (file_exists($controller_route)) {
            
            require_once $controller_route;
            
            if (class_exists($controller_class_name)) {
                
                $ControllerReflectionClass = new ReflectionClass($controller_class_name);

                if ($ControllerReflectionClass->hasMethod($function_name)) {
                    
                    $functionReflection = $ControllerReflectionClass->getMethod($function_name);
                    
                    if (count($parameters) >= $functionReflection->getNumberofRequiredParameters()
                            && count($parameters) <= $functionReflection->getNumberofParameters()) {
                        
                        $this->controllerFile = $controller_route;
                        $this->controller = $controller_class_name;
                        $this->method = $function_name;
                        $this->parameters = $parameters;
                        $this->uri = $uri;
                    }
                }
            }
        }
        return;
    }
    
    public function setRoute($controller_route, $controller_class_name, $function_name, $parameters = array())
    {
        $this->controllerFile   = $controller_route;
        $this->controller       = $controller_class_name;
        $this->method           = $function_name;
        $this->parameters       = $parameters;
    }
        
    public function controllerFile()
    {
        return $this->controllerFile;
    }
    
    public function controller()
    {
        return $this->controller;
    }
    
    public function method()
    {
        return $this->method;
    }
    
    public function parameters()
    {
        return $this->parameters;
    }
}
