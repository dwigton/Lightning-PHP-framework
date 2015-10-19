<?php namespace Lightning;

class Error_Controller
{
    public function notFound()
    {
//        header("HTTP/1.0 404 Not Found");
        echo "<p>404 page not found</p>";
    }
}
