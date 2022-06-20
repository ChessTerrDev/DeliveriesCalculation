<?php

namespace DeliveriesCalculation\Entity\Response;

use DeliveriesCalculation\Entity\AbstractDeliveryEntity;

class DeliveryResponse extends AbstractDeliveryEntity
{
    /**
     * Название тарифа.
     * @var string
     */
    protected $name;

    /**
     * Описание.
     * @var string
     */
    protected $description;

    /**
     * Код тарифа.
     * @var int
     */
    protected $code;

    /**
     * Стоимость доставки.
     * @var float
     */
    protected $deliverySum;

    /**
     * Минимальное время доставки (в рабочих днях).
     * @var int
     */
    protected $periodMin;

    /**
     * Максимальное время доставки (в рабочих днях).
     * @var int
     */
    protected $periodMax;


    /**
     * @param array $properties
     */
    public function __construct(array $properties)
    {
        $properties = array_filter($properties, function ($a) {
            return ($a !== null);
        });

        foreach ($properties as $name => $val) {
            if(property_exists($this, $name)) {
                $this->$name = $val;
            }
        }
        return $this;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @return int
     */
    public function getCode(): int
    {
        return $this->code;
    }

    /**
     * @return float
     */
    public function getDeliverySum(): float
    {
        return $this->deliverySum;
    }

    /**
     * @return int
     */
    public function getPeriodMin(): int
    {
        return $this->periodMin;
    }

    /**
     * @return int
     */
    public function getPeriodMax(): int
    {
        return $this->periodMax;
    }
}