<?php

require_once __DIR__ . '/../vendor/autoload.php';

use PHPUnit\Framework\TestCase;

class LocationTest extends TestCase
{

    public function testGetGeoData()
    {
        $location = new \DeliveriesCalculation\Location('178.155.5.66');
        $result = $location->getGeoData();

        $this->assertIsArray($result);
        $this->assertNotEmpty($result);

        $this->assertArrayHasKey('toPostalCode', $result);
        $this->assertArrayHasKey('address', $result);
        $this->assertArrayHasKey('city', $result);
        $this->assertArrayHasKey('geo_lat', $result);
        $this->assertArrayHasKey('geo_lon', $result);
        $this->assertArrayHasKey('region_type', $result);
        $this->assertArrayHasKey('region', $result);
    }
}
