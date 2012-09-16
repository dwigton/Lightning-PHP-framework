<?php
class Output_Formatter_Module_Config
{
    public function config()
    {
        Lightning_Event::addObserver('Render_Complete', 'app/modules/output_formatter/model/observer.php', 'Output_Formatter_Observer', 'formatOutput');
    }
}