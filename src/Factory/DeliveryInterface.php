<?php

namespace DeliveriesCalculation\Factory;

use DeliveriesCalculation\Entity\{Delivery, DeliveryResponse};

interface DeliveryInterface
{
    /**
     * @param \DeliveriesCalculation\Entity\Delivery $delivery Вся информация о посылке, откуда, куда, сколько, как и т.д.
     */
    public function __construct(Delivery $delivery);

    /**
     * При удачном исходе возвращает стоимость доставки
     * @param bool $typeReturnValue тип возвращаемого значения:
     * bool false = Array |
     * bool true = DeliveryResponse (по умолчанию)
     * @return array | object | null
     */
    public function getDeliveryCalculation(bool $typeReturnValue = true): array | DeliveryResponse | null;
}