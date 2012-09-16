<?
class Lightning_Router extends Lightning_Router_Base
{
    protected function initializeRoutes()
    {
    /*
    *   Add Routes to controllers with the addRoute() function
    *   
    *   addRoute("/regex1/regex2/./regexn",
    *            "controller/path",
    *            "name_of_controller_class",
    *            "name_of_method";
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
    *   The Lightning_Router_Base class will choose the last matching route added. Thus it
    *   is a good idea to start with the most general route and add more specific routes lower
    *   in the list.
    */
        $this->addRoute('', 'app/controllers/index_controller.php', 'Index_Controller_Class' , 'indexAction');
        $this->addRoute('[a-z]+', 'app/controllers/index_controller.php', 'Index_Controller_Class', '#1Action');
        $this->addRoute('[a-z]+', 'app/controllers/#1_controller.php', '#1\U_Controller_Class', 'indexAction');
        $this->addRoute('[a-z]+/[a-z]+', 'app/controllers/#1_controller.php', '#1\U_Controller_Class', '#2Action');
    }
}
?>
