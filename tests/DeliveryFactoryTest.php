<?php

require_once __DIR__ . '/../vendor/autoload.php';
if (!session_id()) session_start();

use PHPUnit\Framework\TestCase;
use DeliveriesCalculation\Constants;
use DeliveriesCalculation\Entity\Request\Dimensions;


class DeliveryFactoryTest extends TestCase
{

    public function testSdekDelivery()
    {
        $location = new \DeliveriesCalculation\Location('178.155.5.66');
        $deliveryEntity = new \DeliveriesCalculation\Entity\Request\Delivery(
            $location->getGeoData() // Добавляем почтовый индекс получателя в информацию по доставке. Необязательный параметр.
        );
        $deliveryEntity
            ->setPackagePrice(3658) // Общая стоимость содержимого коробки в рублях
            ->setEstimatedPrice(3656) // Объявленная ценность содержимого коробки.
            ->setDimensions(new Dimensions(1500, 300, 200, 300));

        $sdekDeliveryEntity = clone $deliveryEntity;
        $sdekDeliveryEntity->setFields(Constants::SDEK_DELIVERY);

        $sdekDelivery = \DeliveriesCalculation\Factory\DeliveryFactory::sdekDelivery($sdekDeliveryEntity);
        $sdekDelivery
            ->addTariffs([136, 137])
            ->calculation();

        $result = $sdekDelivery->getResultToArray();

        $this->assertIsArray($result);
        $this->assertNotEmpty($result);

        foreach ($result as $val) {
            $this->assertArrayHasKey('name', $val);
            $this->assertArrayHasKey('deliverySum', $val);
            $this->assertArrayHasKey('periodMin', $val);
            $this->assertArrayHasKey('periodMax', $val);
        }

        $result = $sdekDelivery->getResult();
        $this->assertIsArray($result);
        $this->assertNotEmpty($result);

        foreach ($result as $val) {
            $this->assertIsObject($val);
            $this->assertInstanceOf('DeliveriesCalculation\Entity\Response\DeliveryResponse', $val);
        }

    }

    public function testOzonRocketDelivery()
    {
        $location = new \DeliveriesCalculation\Location('178.155.5.66');
        $deliveryEntity = new \DeliveriesCalculation\Entity\Request\Delivery(
            $location->getGeoData() // Добавляем почтовый индекс получателя в информацию по доставке. Необязательный параметр.
        );
        $deliveryEntity
            ->setPackagePrice(3658) // Общая стоимость содержимого коробки в рублях
            ->setEstimatedPrice(3656) // Объявленная ценность содержимого коробки.
            ->setDimensions(new Dimensions(1500, 300, 200, 300));

        $ozonRocketDeliveryEntity = clone $deliveryEntity;
        $ozonRocketDeliveryEntity
            ->setFields(Constants::OZON_ROCKET_DELIVERY)
            ->setAddress('Мовапвапсква');

        $ozonRocketDelivery = \DeliveriesCalculation\Factory\DeliveryFactory::ozonRocketDelivery($ozonRocketDeliveryEntity);
        $result = $ozonRocketDelivery
            ->calculation();

        $result = $ozonRocketDelivery->getResultToArray();

        $this->assertIsArray($result);
        $this->assertNotEmpty($result);

        foreach ($result as $val) {
            $this->assertArrayHasKey('name', $val);
            $this->assertArrayHasKey('deliverySum', $val);
            $this->assertArrayHasKey('periodMin', $val);
            $this->assertArrayHasKey('periodMax', $val);
        }

        $result = $ozonRocketDelivery->getResult();
        $this->assertIsArray($result);
        $this->assertNotEmpty($result);

        foreach ($result as $val) {
            $this->assertIsObject($val);
            $this->assertInstanceOf('DeliveriesCalculation\Entity\Response\DeliveryResponse', $val);
        }
    }

    public function testPostalRussiaDelivery()
    {
        $location = new \DeliveriesCalculation\Location('178.155.5.66');
        $deliveryEntity = new \DeliveriesCalculation\Entity\Request\Delivery(
            $location->getGeoData() // Добавляем почтовый индекс получателя в информацию по доставке. Необязательный параметр.
        );
        $deliveryEntity
            ->setPackagePrice(3658) // Общая стоимость содержимого коробки в рублях
            ->setEstimatedPrice(3656) // Объявленная ценность содержимого коробки.
            ->setDimensions(new Dimensions(1500, 300, 200, 300));

        $postalDeliveryEntity = clone $deliveryEntity;
        $postalDeliveryEntity->setFields(Constants::POSTAL_DELIVERY);

        $postalDelivery = \DeliveriesCalculation\Factory\DeliveryFactory::postalRussiaDelivery($postalDeliveryEntity);
        $result = $postalDelivery
            ->calculation();

        $result = $postalDelivery->getResultToArray();

        $this->assertIsArray($result);
        $this->assertNotEmpty($result);

        foreach ($result as $val) {
            $this->assertArrayHasKey('name', $val);
            $this->assertArrayHasKey('deliverySum', $val);
            $this->assertArrayHasKey('periodMin', $val);
            $this->assertArrayHasKey('periodMax', $val);
        }

        $result = $postalDelivery->getResult();
        $this->assertIsArray($result);
        $this->assertNotEmpty($result);

        foreach ($result as $val) {
            $this->assertIsObject($val);
            $this->assertInstanceOf('DeliveriesCalculation\Entity\Response\DeliveryResponse', $val);
        }
    }

    public function testBoxberryDelivery()
    {
        $location = new \DeliveriesCalculation\Location('178.155.5.66');
        $deliveryEntity = new \DeliveriesCalculation\Entity\Request\Delivery(
            $location->getGeoData() // Добавляем почтовый индекс получателя в информацию по доставке. Необязательный параметр.
        );
        $deliveryEntity
            ->setPackagePrice(3658) // Общая стоимость содержимого коробки в рублях
            ->setEstimatedPrice(3656) // Объявленная ценность содержимого коробки.
            ->setDimensions(new Dimensions(1500, 300, 200, 300));

        $boxberryDeliveryEntity = clone $deliveryEntity;
        $boxberryDeliveryEntity
            ->setFields(Constants::BOXBERRY_DELIVERY)
            ->setToPointId(19733);

        $boxberryDelivery = \DeliveriesCalculation\Factory\DeliveryFactory::boxberryDelivery($boxberryDeliveryEntity);
        $result = $boxberryDelivery
            ->calculation();

        $result = $boxberryDelivery->getResultToArray();

        $this->assertIsArray($result);
        $this->assertNotEmpty($result);

        foreach ($result as $val) {
            $this->assertArrayHasKey('name', $val);
            $this->assertArrayHasKey('deliverySum', $val);
            $this->assertArrayHasKey('periodMin', $val);
            $this->assertArrayHasKey('periodMax', $val);
        }

        $result = $boxberryDelivery->getResult();
        $this->assertIsArray($result);
        $this->assertNotEmpty($result);

        foreach ($result as $val) {
            $this->assertIsObject($val);
            $this->assertInstanceOf('DeliveriesCalculation\Entity\Response\DeliveryResponse', $val);
        }
    }
}
