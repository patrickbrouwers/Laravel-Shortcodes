<?php

include_once('helpers/BoldShortcode.php');

use Mockery as m;

class TestViewCompiling extends TestCase {

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

        // Register b tag with a callback
        $this->shortcode->register('b', 'BoldShortcode');

        // Add view namespace
        View::addNamespace('shortcodeViews', __DIR__ . '/helpers');

        // Share the string which should be render inside the view
        View::share('string', $this->string);
    }

    public function testNormalRenderWithConfigDisabledShortcodes()
    {
        $view = $this->makeView()->render();

        // Nothing should have happended
        $this->assertEquals($this->string, $view);
    }

    public function testNormalRenderWithDisabledShortcodes()
    {
        // Disable
        $this->shortcode->disable();

        $view = $this->makeView()->render();

        // Nothing should have happended
        $this->assertEquals($this->string, $view);
    }

    public function testNormalRenderWithoutShortcodesEnabledThroughView()
    {
        // Render the view
        $view = $this->makeView()
                    ->withoutShortcodes()
                    ->render();

        // Nothing should have happended
        $this->assertEquals($this->string, $view);
    }

    public function testNormalRenderWithEnabledShortcodes()
    {
        // Enable
        $this->shortcode->enable();

        // Render the view
        $view = $this->makeView()->render();

        // Nothing should have happended
        $this->assertEquals($this->compiled, $view);
    }

    public function testNormalRenderWithShortcodesEnabledThroughView()
    {
        // Render the view
        $view = $this->makeView()
                    ->withShortcodes()
                    ->render();

        // Nothing should have happended
        $this->assertEquals($this->compiled, $view);
    }

    protected function makeView()
    {
        return View::make('shortcodeViews::shortcode');
    }

}