<?php

namespace DeliveriesCalculation\Factory;

use DeliveriesCalculation\Entity\Request\Delivery;

class DeliveryFactory
{
    public static function sdekDelivery(Delivery $delivery): DeliveryInterface
    {
        return new SdekDelivery($delivery);
    }

    public static function postalRussiaDelivery(Delivery $delivery): DeliveryInterface
    {
        return new PostalRussiaDelivery($delivery);
    }

    public static function boxberryDelivery(Delivery $delivery): DeliveryInterface
    {
        return new BoxberryDelivery($delivery);
    }

    public static function ozonRocketDelivery(Delivery $delivery): DeliveryInterface
    {
        return new OzonRocketDelivery($delivery);
    }
}