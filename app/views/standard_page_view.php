<?php
class Standard_Page_View extends Lightening_View{
    public function __construct(){
        $this->setTemplateFile('app/templates/standard_page_template.php');
        $this->addCss('media/css/reset.css');
        $this->addCss('media/css/styles.css');
    }
}
?>
