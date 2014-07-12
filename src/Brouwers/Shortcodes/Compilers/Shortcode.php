<?php namespace Brouwers\Shortcodes\Compilers;

class Shortcode {

    /**
     * Shortcode name
     * @var [type]
     */
    protected $name;

    /**
     * Shortcode Attributes
     * @var array
     */
    protected $attributes = array();

    /**
     * Shortcode content
     * @var [type]
     */
    public $content;

    /**
     * Constructor
     * @param [type] $name       [description]
     * @param array  $attributes [description]
     * @param [type] $content    [description]
     */
    public function __construct($name, $attributes = array(), $content)
    {
        $this->name       = $name;
        $this->attributes = $attributes;
        $this->content    = $content;
    }

    /**
     * Get shortcode name
     * @return [type] [description]
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Get shortcode attributes
     * @return [type] [description]
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Return array of attributes;
     * @return [type] [description]
     */
    public function toArray()
    {
        return $this->attributes;
    }

    /**
     * Dynamically get attributes
     * @param  [type] $param [description]
     * @return [type]        [description]
     */
    public function __get($param)
    {
        return isset($this->attributes[$param]) ? $this->attributes[$param] : null;
    }

}