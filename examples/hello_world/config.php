<?php

/*
*   Add Routes to controllers with the addRoute() function
*   
*   addRoute("/regex1/regex2/./regexn",
*            "controller/path",
*            "name_of_controller_class",
*            "name_of_method");
*   
*   The regular expressions will be put in variables referenceble as #1, #2, #3 etc..
*   All further items in the URI will be sent to the matching method as arguments. 
*   The number of arguments must be greater than or equal to the number of required 
*   parameters for the method and less
*   than or equal to the number of total parameters.
* 
*   An incorrect number of parameters, a non-existant controller class or method, 
*   or incorrect file path will result in calling a default 404 page not found error.
* 
*   The Lightning_Router class will choose the last matching route added. Thus it
*   is a good idea to start with the most general route and add more specific routes lower
*   in the list.
*
*   The following routes are an example of a single path setup where all the
*   controllers are in one place.
*/

    App::addRoute('', 'index_controller.php', 'Index_Controller', 'indexAction');
    
