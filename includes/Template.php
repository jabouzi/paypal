<?php

class Template
{
	private $template = '';
    private $content = '';


    public function __construct($template='')
    {
        $this->loadTemplate($template);
    }

    public function loadTemplate($template)
    {
        $this->template = $template;
        $this->content = $template;
        return $this;
    }

    public function prepareContent($variables)
    {
        $search = array();
        $replace = array();
        foreach($variables as $name => $value) {
            $search[] = '[[' . $name . ']]';
            $replace[] = $value;
        }
        $this->content = str_replace($search, $replace, $this->template);
        return $this;
    }

    public function getTemplate()
    {
        return $this->template;
    }

    public function getContent()
    {
       return $this->content;
    }

}
