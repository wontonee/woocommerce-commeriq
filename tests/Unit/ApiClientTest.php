<?php
namespace CommerIQ\Tests\Unit;

use PHPUnit\Framework\TestCase;
use CommerIQ\API\ApiClient;

class ApiClientTest extends TestCase
{
    public function testSignPayload()
    {
        $client = new ApiClient('https://example.test', 'key', 'secret');
        $body = json_encode(['a' => 1]);
        $ts = 1234567890;
        $expected = hash_hmac('sha256', $ts . '.' . $body, 'secret');
        $this->assertEquals($expected, $client->signPayload($body, $ts));
    }
}
