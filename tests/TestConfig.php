<?php

use Mockery as m;

class TestConfig extends TestCase
{

    public function tearDown()
    {
        parent::tearDown();
        m::close();
    }

    public function testEnabledConfig()
    {
        $this->assertEquals(Config::get('shortcodes::enabled'), false);
    }

}