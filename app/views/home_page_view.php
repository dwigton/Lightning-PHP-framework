<?php
class Home_Page_View extends Lightening_View{
    protected function init(){
        $this->setTemplateFile('app/templates/home_page_template.php');
        $this->addScript('media/script/jquery.js');
        $this->addScript('media/script/lightening-test.js');
    }
}
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
?>
