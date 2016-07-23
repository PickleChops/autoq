<?php

namespace Autoq\Lib\Html;

/**
 * HTML Html Support class
 */
class Html
{

    const TABLE = 'table';
    const TBODY = 'tbody';
    const THEAD = 'thead';
    const TD = 'td';
    const TH = 'th';
    const TR = 'tr';
    const DIV = 'div';
    const SPAN = 'span';
    const LABEL = 'label';
    const A = 'a';
    const H1 = 'h1';
    const H2 = 'h2';
    const H5 = 'h5';
    const P = 'p';
    const STRONG = 'strong';


    private $tag = null;
    private $data = '';
    private $attributes = array();
    private $classNames = array();
    
    /**
     * Constructor for a tag
     * @param null $el
     */
    public function __construct($el= null)
    {
        if ($el) {
            $this->open($el);
        }
    }

    /**
     * Open a Html
     * @param $el
     * @return Html
     */
    public function open($el)
    {
        $this->tag = $el;
        return $this;
    }

    /**
     * Set the content
     * @param $value
     * @return Html
     */
    public function content($value)
    {
        $this->data .= $value;
        return $this;
    }

    /**
     * Return the tag as a string. Call it last!
     * @return string
     */
    public function close()
    {

        $output = '';

        if ($this->tag) {

            $attributeOutput = ' ';

            foreach ($this->attributes as $a) {
                $attributeOutput .= $a . ' ';
            }

            //Add classes in
            if (count($this->classNames)) {
                $attributeOutput .= 'class="' . implode(' ', $this->classNames) . '"';
            }

            $attributeOutput = rtrim($attributeOutput);

            $output = '<' . $this->tag . $attributeOutput . '>' . $this->data . '</' . $this->tag . '>';

        }
        return $output;
    }
    

    /**
     * Add an attribute to the tag
     * @param $attribute
     * @param $value
     * @return Html
     */
    public function attr($attribute, $value)
    {

        $attrStr = $attribute . '=' . $this->quote($value);

        $this->attributes[] = $attrStr;
        return $this;
    }

    /**
     * Add a class
     * @param $value
     * @return T
     */
    public function addClass($value)
    {

        $this->addClasses($value);
        return $this;
    }

    /**
     * Add one or more classes to Html
     * @param $classes
     * @return Html
     */
    public function addClasses($classes)
    {

        if (!is_array($classes)) {
            $this->classNames[] = $classes;
        } else {
            $this->classNames = array_merge($this->classNames, $classes);
        }

        return $this;
    }


    /**
     * Add an ID
     * @param $value
     * @return Html
     */
    public function addId($value)
    {

        $this->attr('id', $value);
        return $this;
    }

    /**
     * Wrap string in quotes. Default is double
     * @param $string
     * @param string $type
     * @return string
     */
    private function quote($string, $type = '"')
    {
        return $type . $string . $type;
    }


}

