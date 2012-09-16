<?php
class Output_Formatter_Module_Config
{
    public function config()
    {
        Lightning_Event::addObserver('Render_Complete', 'modules/output_formatter/models/observer.php', 'Output_Formatter_Observer', 'formatOutput');
    }
}