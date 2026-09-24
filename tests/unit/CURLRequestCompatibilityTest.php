<?php

use CodeIgniter\HTTP\CURLRequest;
use CodeIgniter\Test\CIUnitTestCase;

final class CURLRequestCompatibilityTest extends CIUnitTestCase
{
    public function testClientCanBeCreatedWithoutConnectionSharing(): void
    {
        if (!extension_loaded('curl')) {
            $this->markTestSkipped('Run with the server PHP that provides cURL.');
        }

        $this->assertSame([], config(\Config\CURLRequest::class)->shareConnectionOptions);
        $this->assertInstanceOf(CURLRequest::class, \Config\Services::curlrequest([], null, null, false));
    }
}
