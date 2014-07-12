<?php

include_once('helpers/BoldShortcode.php');

use Mockery as m;

class TestShortcodeRegistration extends TestCase {

    /**
     * String to render
     * @var string
     */
    protected $string   = '[b class="bold"]Test[/b]';

    /**
     * Compiled string
     * @var string
     */
    protected $compiled = '<strong class="bold">Test</strong>';

    public function setUp()
    {
        parent::setUp();
        $this->shortcode = app('shortcode');
    }

    public function testInstance()
    {
        $this->assertInstanceOf('Brouwers\Shortcodes\Shortcode', $this->shortcode);
    }

    public function testCallbackRegistration()
    {
        // Register b tag with a callback
        $this->shortcode->register('b', function($shortcode, $content)
        {
            return '<strong class="'. $shortcode->class .'">' . $content . '</strong>';
        });

        // Compile the string
        $compiled = $this->shortcode->compile($this->string);

        // Test
        $this->assertEquals($this->compiled, $compiled);
    }

    public function testClassRegistration()
    {
        // Register b tag with a callback
        $this->shortcode->register('b', 'BoldShortcode');

        // Compile the string
        $compiled = $this->shortcode->compile($this->string);

        // Test
        $this->assertEquals($this->compiled, $compiled);
    }

    public function testCustomClassRegistration()
    {
        // Register b tag with a callback
        $this->shortcode->register('b', 'BoldShortcode@custom');

        // Compile the string
        $compiled = $this->shortcode->compile($this->string);

        // Test
        $this->assertEquals($this->compiled, $compiled);
    }

}