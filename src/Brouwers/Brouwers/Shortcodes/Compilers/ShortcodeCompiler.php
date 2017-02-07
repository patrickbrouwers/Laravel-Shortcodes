<?php

namespace Brouwers\Shortcodes\Compilers;

use Illuminate\Support\Str;

class ShortcodeCompiler
{

    /**
     * Enabled state
     * @var boolean
     */
    protected $enabled = false;

    /**
     * Registered shortcodes
     * @var array
     */
    protected $registered = array();

    /**
     * Enable
     * @return void
     */
    public function enable()
    {
        $this->enabled = true;
    }

    /**
     * Disable
     * @return void
     */
    public function disable()
    {
        $this->enabled = false;
    }

    /**
     * Add a new shortcode
     * @param string $name
     * @param callable|string $callback
     */
    public function add($name, $callback)
    {
        $this->registered[$name] = $callback;
    }

    /**
     * Compile the contents
     * @param  string $value
     * @return string
     */
    public function compile($value)
    {
        // Only continue is shortcodes have been registered
        if (!$this->enabled || !$this->hasShortcodes()) {
            return $value;
        }

        // Set empty result
        $result = '';

        // Here we will loop through all of the tokens returned by the Zend lexer and
        // parse each one into the corresponding valid PHP. We will then have this
        // template as the correctly rendered PHP that can be rendered natively.
        foreach (token_get_all($value) as $token) {
            $result .= is_array($token) ? $this->parseToken($token) : $token;
        }

        return $result;
    }

    /**
     * Check if shortcodes have been registered
     * @return boolean
     */
    public function hasShortcodes()
    {
        return !empty($this->registered);
    }

    /**
     * Parse the tokens from the template.
     * @param  array  $token
     * @return string
     */
    protected function parseToken($token)
    {
        list($id, $content) = $token;

        if ($id == T_INLINE_HTML) {
            $content = $this->renderShortcodes($content);
        }

        return $content;
    }

    /**
     * Render shortcodes
     * @param  string  $value
     * @return string
     */
    protected function renderShortcodes($value)
    {
        return preg_replace_callback($this->getRegex(), array(
            &$this,
            'render'
        ), $value);
    }

    /**
     * Render the current calld shortcode.
     * @param  array  $matches
     * @return string
     */
    public function render($matches)
    {
        // Compile the shortcode
        $compiled = $this->compileShortcode($matches);

        // Render the shortcode through the callback
        return call_user_func_array($this->getCallback(), array(
            $compiled,
            $compiled->getContent(),
            $this,
            $compiled->getName()
        ));
    }

    /**
     * Get Compiled Attributes.
     * @return \Brouwers\Shortcodes\Shortcode
     */
    protected function compileShortcode($matches)
    {
        // Set matches
        $this->setMatches($matches);

        // pars the attributes
        $attributes = $this->parseAttributes($this->matches[3]);

        // return shortcode instance
        return new Shortcode(
            $this->getName(),
            $attributes,
            $this->getContent()
        );
    }

    /**
     * Set the macthes
     * @param array $matches
     */
    protected function setMatches($matches = array())
    {
        $this->matches = $matches;
    }

    /**
     * Return the shortcode name
     * @return string
     */
    public function getName()
    {
        return $this->matches[2];
    }

    /**
     * Return the shortcode content
     * @return string
     */
    public function getContent()
    {
        // Compile the content, to support nested shortcodes
        return $this->compile($this->matches[5]);
    }

    /**
     * Get the callback for the current shortcode (class or callback)
     * @param  string  $name
     * @return callable|array
     */
    public function getCallback()
    {
        // Get the callback from the shortcodes array
        $callback = $this->registered[$this->getName()];

        // if is a string
        if (is_string($callback)) {
            // Parse the callback
            list($class, $method) = Str::parseCallback($callback, 'register');

            // If the class exist
            if (class_exists($class)) {
                // return class and method
                return array(
                    app($class),
                    $method
                );
            }
        }

        return $callback;
    }

    /**
     * Parse the shortcode attributes
     * @author Wordpress
     * @return array
     */
    protected function parseAttributes($text)
    {
        $attributes = array();

        // attributes pattern
        $pattern = '/(\w+)\s*=\s*"([^"]*)"(?:\s|$)|(\w+)\s*=\s*\'([^\']*)\'(?:\s|$)|(\w+)\s*=\s*([^\s\'"]+)(?:\s|$)|"([^"]*)"(?:\s|$)|(\S+)(?:\s|$)/';

        // Match
        if (preg_match_all($pattern, preg_replace("/[\x{00a0}\x{200b}]+/u", " ", $text), $match, PREG_SET_ORDER)) {
            foreach ($match as $m) {
                if (!empty($m[1])) {
                    $attributes[strtolower($m[1])] = stripcslashes($m[2]);
                } else if (!empty($m[3])) {
                    $attributes[strtolower($m[3])] = stripcslashes($m[4]);
                } else if (!empty($m[5])) {
                    $attributes[strtolower($m[5])] = stripcslashes($m[6]);
                } else if (isset($m[7]) and strlen($m[7])) {
                    $attributes[] = stripcslashes($m[7]);
                } else if (isset($m[8])) {
                    $attributes[] = stripcslashes($m[8]);
                }
            }
        } else {
            $attributes = ltrim($text);
        }

        // return attributes
        return is_array($attributes) ? $attributes : array($attributes);
    }

    /**
     * Get shortcode regex.
     * @author Wordpress
     * @return string
     */
    protected function getRegex()
    {
        // Get shortcode names
        $shortcodeNames = $this->getShortcodeNames();

        // return regex
        return  "/"
                . '\\['                              // Opening bracket
                . '(\\[?)'                           // 1: Optional second opening bracket for escaping shortcodes: [[tag]]
                . "($shortcodeNames)"                // 2: Shortcode name
                . '(?![\\w-])'                       // Not followed by word character or hyphen
                . '('                                // 3: Unroll the loop: Inside the opening shortcode tag
                .     '[^\\]\\/]*'                   // Not a closing bracket or forward slash
                .     '(?:'
                .         '\\/(?!\\])'               // A forward slash not followed by a closing bracket
                .         '[^\\]\\/]*'               // Not a closing bracket or forward slash
                .     ')*?'
                . ')'
                . '(?:'
                .     '(\\/)'                        // 4: Self closing tag ...
                .     '\\]'                          // ... and closing bracket
                . '|'
                .     '\\]'                          // Closing bracket
                .     '(?:'
                .         '('                        // 5: Unroll the loop: Optionally, anything between the opening and closing shortcode tags
                .             '[^\\[]*+'             // Not an opening bracket
                .             '(?:'
                .                 '\\[(?!\\/\\2\\])' // An opening bracket not followed by the closing shortcode tag
                .                 '[^\\[]*+'         // Not an opening bracket
                .             ')*+'
                .         ')'
                .         '\\[\\/\\2\\]'             // Closing shortcode tag
                .     ')?'
                . ')'
                . '(\\]?)'                         // 6: Optional second closing brocket for escaping shortcodes: [[tag]]
                . "/s";
    }

    /**
     * Get shortcode names
     * @return string
     */
    protected function getShortcodeNames()
    {
        return join('|', array_map('preg_quote', array_keys($this->registered)));
    }
}
