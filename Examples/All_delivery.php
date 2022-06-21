<?php
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);
require_once '../vendor/autoload.php';

if (!session_id()) session_start();

use DeliveriesCalculation\Constants;
use DeliveriesCalculation\Entity\Request\Dimensions;

$location = new DeliveriesCalculation\Location('178.155.5.66');
var_dump($location->getGeoData());
/**
 * Получаем локацию пользователя, получателя (город, почтовый индекс)
 */
$location = new DeliveriesCalculation\Location('178.155.5.66');

/**
 * Создаем сущность с информацией о посылке
 */
$deliveryEntity = new \DeliveriesCalculation\Entity\Request\Delivery(
    $location->getGeoData() // Добавляем почтовый индекс получателя в информацию по доставке. Необязательный параметр.
);
// Добавляем информацию о посылке (товаре, заказе)
$deliveryEntity
    ->setPackagePrice(3658) // Общая стоимость содержимого коробки в рублях
    ->setEstimatedPrice(3656) // Объявленная ценность содержимого коробки.
    ->setDimensions(new Dimensions(1500, 300, 200, 300));
/**
 * Все getЕры и setЕры класса deliveryEntity
 */

/**
 * SDEKDelivery
 * Делаем копию основной информации о доставке
 */
$sdekDeliveryEntity = clone $deliveryEntity;
// Добавляем (заменяем) авторизационные данные
$sdekDeliveryEntity->setFields(Constants::SDEK_DELIVERY);

// Инициализируем клиента доставки
$sdekDelivery = DeliveriesCalculation\Factory\DeliveryFactory::sdekDelivery($sdekDeliveryEntity);
// Добавляем настройки и считаем доставку
$result = $sdekDelivery
    ->addTariffs([136, 137])
    ->calculation()
    ->getResult();
var_dump($result);



// PostalRussiaDelivery ---------------------------------
$postalDeliveryEntity = clone $deliveryEntity;
$postalDeliveryEntity->setFields(Constants::POSTAL_DELIVERY);

$postalDelivery = DeliveriesCalculation\Factory\DeliveryFactory::postalRussiaDelivery($postalDeliveryEntity);
$result = $postalDelivery
    ->calculation()
    ->getResult();
var_dump($result);

// BoxberryDelivery
$boxberryDeliveryEntity = clone $deliveryEntity;
$boxberryDeliveryEntity
    ->setFields(Constants::BOXBERRY_DELIVERY)
    ->setToPointId(19733);

$boxberryDelivery = DeliveriesCalculation\Factory\DeliveryFactory::boxberryDelivery($boxberryDeliveryEntity);
$result = $boxberryDelivery
    ->calculation()
    ->getResult();
var_dump($result);


$ozonRocketDeliveryEntity = clone $deliveryEntity;
$ozonRocketDeliveryEntity
    ->setFields(Constants::OZON_ROCKET_DELIVERY)
    ->setAddress('Мовапвапсква');

$ozonRocketDelivery = DeliveriesCalculation\Factory\DeliveryFactory::ozonRocketDelivery($ozonRocketDeliveryEntity);
$result = $ozonRocketDelivery
    ->calculation()
    ->getResult();
var_dump($result);
