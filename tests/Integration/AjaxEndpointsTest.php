<?php
namespace CommerIQ\Tests\Integration;

use PHPUnit\Framework\TestCase;

class AjaxEndpointsTest extends TestCase
{
    public function setUp(): void
    {
        if (!function_exists('add_action')) {
            $this->markTestSkipped('WordPress not available in this environment');
        }
    }

    public function testPlaceholder()
    {
        $this->assertTrue(true);
    }
}
