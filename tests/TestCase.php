<?php

use Orchestra\Testbench\TestCase as TestBenchTestCase;

class TestCase extends TestBenchTestCase
{

    public function testShortcodeClass()
    {
        $shortcode = App::make('shortcode');
        $this->assertInstanceOf('Brouwers\Shortcodes\Shortcode', $shortcode);
    }

    protected function getPackageProviders()
    {
        return array('Brouwers\Shortcodes\ShortcodesServiceProvider');
    }

    protected function getPackagePath()
    {
        return realpath(implode(DIRECTORY_SEPARATOR, array(
            __DIR__,
            '..',
            'src',
            'Brouwers',
            'Shortcodes'
        )));
    }

}