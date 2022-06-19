<?php

namespace DeliveriesCalculation\Factory;

use WildTuna\BoxberrySdk\{Client, Entity\CalculateParams};
use DeliveriesCalculation\{
    Constants,
    Entity\Delivery,
    Entity\DeliveryResponse,
    Exception\DeliveryException,
    Logger\Log
};


class BoxberryDelivery extends AbstractDelivery implements DeliveryInterface
{
    private Client $client;

    public function __construct(Delivery $delivery)
    {
        $this->delivery = $delivery;

        if ($this->delivery->isActive() and $this->delivery->getToken()) {
            $this->client = new Client(120, 'https://api.boxberry.ru/json.php');
            $this->client->setToken('main', $this->delivery->getToken());
        }


    }

    public function calculation(): DeliveryInterface
    {
        if (!$this->delivery->isActive()) {
            (new Log($this::class))->addLogInfo(
                $this->delivery->getName() . ': ' . Constants::LOG_MESSAGE['NO_ACTIVE']
            );
            return $this;
        }
        $this->checkParameters();

        $calcParams = new CalculateParams();
        $calcParams->setWeight($this->delivery->getDimension('weight'));
        $calcParams->setPvz($this->delivery->getToPointId());
        $calcParams->setAmount($this->delivery->getPackagePrice());

        $result = $this->client->calcTariff($calcParams);

        if (empty($result)) {
            (new Log($this::class))->addLogInfo(
                Constants::POSTAL_DELIVERY['name'] . ': ' . Constants::LOG_MESSAGE['NO_RESULT']
            );
        } else {
            $this->setResult($result);
        }

        return $this;
    }

    private function checkParameters(): void
    {
        if (!$this->delivery->getToPointId()) {
            (new Log($this::class))->addLogError(
                $this->delivery->getName() . ': ' . Constants::ERRORS['NOT_FROM_POINT']
            );
            throw new DeliveryException(DeliveryException::getErrorMessage('NOT_FROM_POINT', $this->delivery->getName()));
        }
        if (!$this->delivery->getPackagePrice()) {
            (new Log($this::class))->addLogError(
                $this->delivery->getName() . ': ' . Constants::ERRORS['NOT_PRICE']
            );
            throw new DeliveryException(DeliveryException::getErrorMessage('NOT_PRICE', $this->delivery->getName()));
        }
        if (!$this->delivery->getDimension('weight')) {
            (new Log($this::class))->addLogError(
                $this->delivery->getName() . ': ' . Constants::ERRORS['NOT_WEIGHT']
            );
            throw new DeliveryException(DeliveryException::getErrorMessage('NOT_WEIGHT', $this->delivery->getName()));
        }
    }

    public function setResult(object|array $result): void
    {
        $this->result = new DeliveryResponse(
            [
                'name' => $this->delivery->getName(),
                'description' => $this->delivery->getDescription(),
                'deliverySum' => round($result->getPrice()),
                'periodMin' => $result->getDeliveryPeriod(),
                'periodMax' => $result->getDeliveryPeriod()
            ]
        );
    }

    /**
     * Возвращает результат в виде объекта DeliveryResponse
     * @return \DeliveriesCalculation\Entity\DeliveryResponse|null
     */
    public function getResult(): ?DeliveryResponse
    {
        return $this->result;
    }

    /**
     * Возвращает результат в виде массива
     * @return array|null
     */
    public function getResultToArray(): ?array
    {
        return $this->parseField($this->result);
    }
}