<?php

namespace DeliveriesCalculation\Entity\Request;

use DeliveriesCalculation\Entity\AbstractDeliveryEntity;

class Delivery  extends AbstractDeliveryEntity
{
    private string $name;
    private string $description;

    private bool $active;
    private ?string $account = null;
    private ?string $secure = null;
    private ?string $token = null;
    private ?string $key = null;

    private string $type_client;

    /**
     * @var \DeliveriesCalculation\Entity\Request\Dimensions Информация о габаритах.
     */
    private Dimensions $dimensions;

    /**
     * @var float | null Общая стоимость содержимого коробки в рублях.
     */
    private ?float $packagePrice = null;

    /**
     * @var int
     */
    private int $count = 1;

    /**
     * @var float | null Объявленная ценность содержимого коробки.
     */
    private ?float $estimatedPrice = null;

    /**
     * @var string | null Адрес доставки.
     */
    private ?string $address = null;

    /**
     * @var string | null Пункт назначения
     */
    private ?string $toPoint = null;
    private ?int $toPointId = null;
    private ?int $toPostalCode = null;


    /**
     * @var string | null Пункт отправления
     */
    private ?string $fromPoint = null;
    private ?int $fromPointId = null;
    private ?int $fromPostalCode = null;


    public function __construct(?array $startSettings = null)
    {
        if (!empty($startSettings)) {
            $this->setFields($startSettings);
        }
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return Delivery
     */
    public function setName(string $name): Delivery
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @param string $description
     * @return Delivery
     */
    public function setDescription(string $description): Delivery
    {
        $this->description = $description;
        return $this;
    }

    /**
     * @return bool
     */
    public function isActive(): bool
    {
        return $this->active;
    }

    /**
     * @param bool $active
     * @return Delivery
     */
    public function setActive(bool $active): Delivery
    {
        $this->active = $active;
        return $this;
    }

    /**
     * @return ?string
     */
    public function getAccount(): ?string
    {
        return $this->account;
    }

    /**
     * @param string $account
     * @return Delivery
     */
    public function setAccount(string $account): Delivery
    {
        $this->account = $account;
        return $this;
    }

    /**
     * @return ?string
     */
    public function getSecure(): ?string
    {
        return $this->secure;
    }

    /**
     * @param string $secure
     * @return Delivery
     */
    public function setSecure(string $secure): Delivery
    {
        $this->secure = $secure;
        return $this;
    }

    /**
     * @return ?string
     */
    public function getToken(): ?string
    {
        return $this->token;
    }

    /**
     * @param string $token
     * @return Delivery
     */
    public function setToken(string $token): Delivery
    {
        $this->token = $token;
        return $this;
    }

    /**
     * @return ?string
     */
    public function getKey(): ?string
    {
        return $this->key;
    }

    /**
     * @param string $key
     * @return Delivery
     */
    public function setKey(string $key): Delivery
    {
        $this->key = $key;
        return $this;
    }

    /**
     * @return string
     */
    public function getTypeClient(): string
    {
        return $this->type_client;
    }

    /**
     * @param string $type_client
     * @return Delivery
     */
    public function setTypeClient(string $type_client): Delivery
    {
        $this->type_client = $type_client;
        return $this;
    }

    /**
     * @return int
     */
    public function getCount(): int
    {
        return $this->count;
    }

    /**
     * @param int $count
     * @return Delivery
     */
    public function setCount(int $count): Delivery
    {
        $this->count = $count;
        return $this;
    }

    /**
     * @return \DeliveriesCalculation\Entity\Request\Dimensions
     */
    public function getDimensions(): Dimensions
    {
        return $this->dimensions;
    }

    /**
     * @param string $param
     * weight - Вес в граммах. |
     * length - Длина в миллиметрах. |
     * height - Высота в миллиметрах. |
     * width - Ширина в миллиметрах. |
     * volume - length * height * width. |
     * @return int|null
     */
    public function  getDimension($param): ?int
    {
        $getMethod = 'get' . ucfirst($param);
        if(method_exists($this->dimensions, $getMethod)) {
            return $this->dimensions->$getMethod();
        }
        return null;
    }

    /**
     * @param \DeliveriesCalculation\Entity\Request\Dimensions $dimensions
     * @return Delivery
     */
    public function setDimensions(\DeliveriesCalculation\Entity\Request\Dimensions $dimensions): Delivery
    {
        $this->dimensions = $dimensions;
        return $this;
    }

    /**
     * @return float|null
     */
    public function getPackagePrice(): ?float
    {
        return $this->packagePrice;
    }

    /**
     * @param float|null $packagePrice
     * @return Delivery
     */
    public function setPackagePrice(?float $packagePrice): Delivery
    {
        $this->packagePrice = $packagePrice;
        return $this;
    }

    /**
     * @return float|null
     */
    public function getEstimatedPrice(): ?float
    {
        return $this->estimatedPrice;
    }

    /**
     * @param float|null $estimatedPrice
     * @return Delivery
     */
    public function setEstimatedPrice(?float $estimatedPrice): Delivery
    {
        $this->estimatedPrice = $estimatedPrice;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getAddress(): ?string
    {
        return $this->address;
    }

    /**
     * @param string|null $address
     * @return Delivery
     */
    public function setAddress(?string $address): Delivery
    {
        $this->address = $address;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getToPoint(): ?string
    {
        return $this->toPoint;
    }

    /**
     * @param string|null $toPoint
     * @return Delivery
     */
    public function setToPoint(?string $toPoint): Delivery
    {
        $this->toPoint = $toPoint;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getToPointId(): ?int
    {
        return $this->toPointId;
    }

    /**
     * @param int|null $toPointId
     * @return Delivery
     */
    public function setToPointId(?int $toPointId): Delivery
    {
        $this->toPointId = $toPointId;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getToPostalCode(): ?int
    {
        return $this->toPostalCode;
    }

    /**
     * @param int|null $toPostalCode
     * @return Delivery
     */
    public function setToPostalCode(?int $toPostalCode): Delivery
    {
        $this->toPostalCode = $toPostalCode;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getFromPoint(): ?string
    {
        return $this->fromPoint;
    }

    /**
     * @param string|null $fromPoint
     * @return Delivery
     */
    public function setFromPoint(?string $fromPoint): Delivery
    {
        $this->fromPoint = $fromPoint;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getFromPointId(): ?int
    {
        return $this->fromPointId;
    }

    /**
     * @param int|null $fromPointId
     * @return Delivery
     */
    public function setFromPointId(?int $fromPointId): Delivery
    {
        $this->fromPointId = $fromPointId;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getFromPostalCode(): ?int
    {
        return $this->fromPostalCode;
    }

    /**
     * @param int|null $fromPostalCode
     * @return Delivery
     */
    public function setFromPostalCode(?int $fromPostalCode): Delivery
    {
        $this->fromPostalCode = $fromPostalCode;
        return $this;
    }


    public function setFields(array $startSettings): Delivery
    {
        $startSettings = array_filter($startSettings, function ($a) {
            return ($a !== null);
        });
        foreach ($startSettings as $name => $val
        ) {
            $setMethod = 'set' . ucfirst($name);
            if (is_array($val) && !property_exists($this, $name)) {
                $this->setFields($val);
            } elseif(property_exists($this, $name) && method_exists($this, $setMethod)) {
                $this->$setMethod($val);
            }
        }
        return $this;
    }

}