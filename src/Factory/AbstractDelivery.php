<?php

namespace DeliveriesCalculation\Factory;

use DeliveriesCalculation\Entity\{Request\Delivery, Response\DeliveryResponse};

abstract class AbstractDelivery
{
    /**
     * @var \DeliveriesCalculation\Entity\Request\Delivery
     */
    private Delivery $delivery;
    private ?DeliveryResponse $result = null;


    /**
     * Возвращает массив с параметрами объекта
     * @param $result
     * @return array|mixed
     */
    protected function parseField($result)
    {
        if (is_array($result)) {
            $retFieldsArr = [];
            foreach ($result as $val) {
                $retFieldsArr[] = $this->parseField($val);
            }
            return $retFieldsArr;
        } elseif (is_object($result) && method_exists($result, 'getFields')) {
            return $result->getFields();
        } else {
            return $result;
        }
    }


}