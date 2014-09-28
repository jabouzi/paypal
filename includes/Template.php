<?php

/*
 * This file is part of the Pepper framework.
 * (c) 2005-2012 Louis-Philippe Favreau
 *
 * For the full copyright and license information, please view
 * the license.txt file that is included in this project.
 *
 * http://www.pepperframework.org/
 *
 */

/**
 * @package pepper
 */
class Template
{

    private $template = '';
    private $content = '';

    /**
     * Constructor
     *
     * @param string $template Optional template. See loadTemplate().
     */
    public function __construct($template='')
    {
        $this->loadTemplate($template);
    }

    /**
     * Load template. Use variables like this in your template:
     *   $template = 'Hello [[firstname]], how are you?';
     *
     * @param string $template The template to use. Call prepareContent()
     *   method afterwards to replace the variables in your template.
     * @return SimpleTemplate Current instance to chain methods.
     */
    public function loadTemplate($template)
    {
        $this->template = $template;
        $this->content = $template;
        return $this;
    }

    /**
     * Will prepare the content based on the template with replaced variables.
     *
     * @param array $variables Associative array with the variables to search
     *   and replace in the templates. If you have [[firstname]] and [[lastname]]
     *   for example in your templates, pass this array:
     *       $variables = array(
     *           'firstname' => 'John',
     *           'lastname' => 'Smith',
     *       );
     * @return SimpleTemplate Current instance to chain methods.
     */
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

    /**
     * Returns the template.
     *
     * @return string The raw template, no values replaced.
     */
    public function getTemplate()
    {
        return $this->template;
    }

    /**
     * Returns the content.
     *
     * @return string The content, with variables replaced
     *   if prepareContent() was called.
     */
    public function getContent()
    {
       return $this->content;
    }

}
