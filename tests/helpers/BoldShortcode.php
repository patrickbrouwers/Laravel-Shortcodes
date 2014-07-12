<?php

class BoldShortcode {

    /**
     * Register the shortcode
     * @param  \Brouwers\Shortcode\Shortcode $shortcode
     * @param  string $content
     * @return string
     */
    public function register($shortcode, $content)
    {
        return '<strong class="'. $shortcode->class .'">' . $content . '</strong>';
    }

    /**
     * Custom shortcode rendering
     * @param  \Brouwers\Shortcode\Shortcode $shortcode
     * @param  string $content
     * @return string
     */
    public function custom($shortcode, $content)
    {
        return '<strong class="'. $shortcode->class .'">' . $content . '</strong>';
    }

}