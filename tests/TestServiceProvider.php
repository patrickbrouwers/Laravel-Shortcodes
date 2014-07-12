<?php

use Brouwers\Shortcodes\ShortcodesServiceProvider;
use Mockery as m;

class TestServiceProvider extends TestCase
{

    public function setUp()
    {
        parent::setUp();
    }

    public function tearDown()
    {
        parent::tearDown();
        m::close();
    }

    public function testProviders()
    {
        $app = m::mock('Illuminate\Foundation\Application');
        $provider = new ShortcodesServiceProvider($app);

        $this->assertCount(3, $provider->provides());

        $this->assertContains('shortcode', $provider->provides());
        $this->assertContains('shortcode.compiler', $provider->provides());
        $this->assertContains('view', $provider->provides());
    }
}