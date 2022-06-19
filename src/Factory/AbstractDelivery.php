<?php

namespace DeliveriesCalculation\Factory;

use DeliveriesCalculation\Entity\{Delivery, DeliveryResponse};

abstract class AbstractDelivery
{
    /**
     * @var \DeliveriesCalculation\Entity\Delivery
     */
    private Delivery $delivery;
    private ?DeliveryResponse $result = null;


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