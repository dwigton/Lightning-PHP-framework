<?php
class Lightning_View
{
    private $child_views = array();
    private $items = array();
    protected $var = array();
    protected $template_file_path;
    protected $item_templates = array();
    
    public function __construct($template_file_path)
    {
        $this->template_file_path = $template_file_path;
        $this->setItemTemplate('css','lightning/templates/items/csstag.php');
        $this->setItemTemplate('script','lightning/templates/items/scripttag.php');
    }
    
    public static function newExtendedView($view_file_path, $view_class_name, $template_file_path = null)
    {
        require_once $view_file_path;
        return new $view_class_name($template_file_path);
    }
    
    public function addChild($handle, $child_view)
    {
        $this->child_views[$handle] = $child_view;
        return $this;
    }
    
    public function addNewChild($handle, $template_file_path)
    {
        $this->child_views[$handle] = new Lightning_View($template_file_path);
        return $this;
    }
    
    public function addNewExtendedChild($handle, $view_file_path, $view_class_name)
    {
        require_once $view_file_path;
        $this->child_views[$handle] = new $view_class_name($view_file_path);
        return $this;
    }
    
    public function getChild($handle)
    {
        return $this->child_views[$handle];
    }
     
    public function setVar($handle, $value)
    {
        $this->var[$handle]=$value;
        return $this;
    }
    
    public function getVar($handle)
    {
        return $this->var[$handle];
    }
    
    public function setTemplateFile($template_file_path)
    {
        $this->template_file_path = $template_file_path;
        return $this;
    }
    
    public function render($variable_array = false)
    {
        Lightning_Event::raiseEvent('lightning_view_render', array('view'=>$this));
        if (is_array($variable_array)) {
            $this->var = array_merge($this->var, $variable_array);
        }
        extract($this->var);
        require App::getTemplate($this->template_file_path);
        return $this;
    }
    
    protected function renderChild($handle)
    {
        $this->child_views[$handle]->render($this->var);
        return $this;
    }

    public function setItemTemplate($type, $template_file_path)
    {
        $this->item_templates[$type] = $template_file_path;
        return $this;
    }

    public function getItemTemplate($type)
    {
        return App::getTemplate($this->item_templates[$type]);
    }
    
    public function addItem($type, $info)
    {
        if (!array_key_exists($type, $this->items))
            $this->items[$type] = array();
        $this->items[$type][$info] = $info;
        return $this;
    }
    
    protected function renderItems($type)
    {
        foreach ($this->itemArray($type) as $item)
            include $this->getItemTemplate($type);
        return $this;
    }
    
    public function itemArray($type)
    {
        if (isset($this->items[$type])) {
            $itemArray = $this->items[$type];
        } else {
            $itemArray = array();
        }
        foreach ($this->child_views as $child_view) {
            $itemArray = array_merge($itemArray, $child_view->itemArray($type));
        }
        return $itemArray;
    }
}
