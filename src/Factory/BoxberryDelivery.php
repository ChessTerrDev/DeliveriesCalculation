<?php

namespace DeliveriesCalculation\Factory;

use DeliveriesCalculation\Entity\Delivery;

class BoxberryDelivery extends AbstractDelivery implements DeliveryInterface
{

    public function __construct(Delivery $delivery)
    {
    }

    public function calculation(): DeliveryInterface
    {
        return $this;
    }

    public function setResult(object|array $result): void
    {
    }

    public function getResult()
    {
    }

    public function getResultToArray(): ?array
    {
        return [];
    }
}