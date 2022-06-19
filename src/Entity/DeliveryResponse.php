<?php

namespace DeliveriesCalculation\Entity;

class DeliveryResponse
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


    /**
     * Выполнить все методы get|is для всех свойств объекта
     * @return array = со свойствами объекта в качестве ключей и внутренними значениями этих свойств в качестве значений
     * ключи возврата только для заданных свойств
     */
    public function getFields(): array
    {
        $vars = array_filter(get_object_vars($this), function ($a) {
            return ($a !== null);
        }); //excluding all null properties from return
        return $this->parseFields($vars);
    }

    /**
     * execute все методы get|is для всех свойств объекта
     * @return array = со всеми свойствами объекта в качестве ключей и внутренними значениями этих свойств в качестве значений
     * returns ключи для Всех свойств, в том числе и не заданных
     */
    public function getAllFields(): array
    {
        $vars = get_object_vars($this);
        return $this->parseFields($vars);
    }

    /**
     * @param $fields
     * @return array
     */
    public function parseFields($fields): array
    {
        $retFieldsArr = [];
        foreach ($fields as $name => $val) {
            $getMethod = 'get' . ucfirst($name);
            $isMethod = 'is' . ucfirst($name);
            if (method_exists($this, $getMethod)) {
                $retFieldsArr[$name] = $this->parseField($this->$getMethod());
            } elseif (method_exists($this, $isMethod)) {
                $retFieldsArr[$name] = $this->parseField($this->$isMethod());
            } else {
                $retFieldsArr[$name] = $this->parseField($val);
            }
        }
        return $retFieldsArr;
    }

    /**
     * @param $val
     * @return array|mixed
     */
    protected function parseField($val)
    {
        if (is_array($val)) {
            return $this->parseFields($val);
        } elseif (is_object($val) && method_exists($val, 'getFields')) {
            return $val->getFields();
        } else {
            return $val;
        }
    }

}