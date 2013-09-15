<?php
class Lightning_Connection
{
    private $settings = array();
    
    public function settings()
    {
        return $this->settings;
    }
    
    public function get($setting)
    {
        return $this->settings[$setting];
    }
    
    public function setSettings(array $settings)
    {
        $this->settings = $settings;
    }
}