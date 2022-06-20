<?php

namespace DeliveriesCalculation\Factory;

use DeliveriesCalculation\Entity\{Request\Delivery, Response\DeliveryResponse};

interface DeliveryInterface
{
    /**
     * @param \DeliveriesCalculation\Entity\Request\Delivery $delivery Вся информация о посылке, откуда, куда, сколько, как и т.д.
     */
    public function __construct(Delivery $delivery);

    /**
     * При удачном исходе получает стоимость доставки
     * @return DeliveryInterface
     */
    public function calculation(): DeliveryInterface;

    /**
     * Ставит результат расчета стоимости доставки
     * @param array|object $result
     * @return void
     */
    public function setResult(array|object $result): void;

    /**
     * Возвращает результат в виде объекта DeliveryResponse
     * @return \DeliveriesCalculation\Entity\Response\DeliveryResponse|null
     */
    public function getResult();

    /**
     * Возвращает результат в виде массива
     * @return array|null
     */
    public function getResultToArray(): ?array;
}