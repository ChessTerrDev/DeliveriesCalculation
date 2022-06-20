<?php

namespace DeliveriesCalculation\Factory;

use LapayGroup\RussianPost\{
    Exceptions\RussianPostException,
    Providers\OtpravkaApi,
    ParcelInfo
};
use DeliveriesCalculation\{
    Constants,
    Exception\DeliveryException,
    Entity\Request\Delivery,
    Entity\Response\DeliveryResponse,
    Logger\Log
};

class PostalRussiaDelivery extends AbstractDelivery implements DeliveryInterface
{
    private OtpravkaApi $client;

    /**
     * Инициализирует клиента LapayGroup\RussianPost\Providers\OtpravkaApi
     * @param Delivery $delivery Вся информация о посылке, откуда, куда, сколько, как и т.д.
     */
    public function __construct(Delivery $delivery)
    {
        $this->delivery = $delivery;
        if ($this->delivery->isActive() and $this->delivery->getToken() and $this->delivery->getKey())
        {
            $this->client = new OtpravkaApi([
                'auth' => [
                    'otpravka' => [
                        'token' => $this->delivery->getToken(),
                        'key' 	=> $this->delivery->getKey()
                    ],
                    'tracking' => [
                        'login' 	=> $this->delivery->getAccount(),
                        'password' 	=> $this->delivery->getSecure()
                    ]
                ]
            ]);
        }
    }

    /**
     * При удачном исходе получает стоимость доставки
     * @return $this
     * @throws \DeliveriesCalculation\Exception\DeliveryException
     */
    public function calculation(): PostalRussiaDelivery
    {
        if (!$this->delivery->isActive()) {
            (new Log($this::class))->addLogInfo(
                Constants::LOG_MESSAGE['NO_ACTIVE'] . $this->delivery->getName()
            );
            return $this;
        }
        $this->checkParameters();

        try {
            $parcelInfo = new ParcelInfo();
            $parcelInfo->setIndexFrom($this->delivery->getFromPostalCode()); // Индекс пункта сдачи из функции $OtpravkaApi->shippingPoints()
            $parcelInfo->setIndexTo($this->delivery->getToPostalCode());
            $parcelInfo->setMailCategory('ORDINARY'); // https://otpravka.pochta.ru/specification#/enums-base-mail-category
            $parcelInfo->setMailType('POSTAL_PARCEL'); // https://otpravka.pochta.ru/specification#/enums-base-mail-type
            $parcelInfo->setWeight($this->delivery->getDimension('weight'));
            $parcelInfo->setFragile(true);

            $result = $this->client->getDeliveryTariff($parcelInfo);
        } catch (RussianPostException | \Exception $e) {
            (new Log($this::class))->addLogError(
                Constants::ERRORS['ERROR_RESPONSE'] . $this->delivery->getName(),
                (array)$e
            );
        }

        if (empty($result)) {
            (new Log($this::class))->addLogInfo(
                Constants::LOG_MESSAGE['NO_RESULT'] . $this->delivery->getName(),
                $this->delivery->getFields()
            );
        } else {
            $this->setResult($result);
        }

        return $this;
    }

    /**
     * Проверяет наличие необходимых параметров
     * @return void
     * @throws \DeliveriesCalculation\Exception\DeliveryException
     */
    private function checkParameters(): void
    {
        if (!$this->delivery->getFromPostalCode()) {
            (new Log($this::class))->addLogError(
                Constants::ERRORS['NOT_FROM_POINT'] . $this->delivery->getName()
            );
            throw new DeliveryException(DeliveryException::getErrorMessage('NOT_FROM_POINT', $this->delivery->getName()));
        }
        if (!$this->delivery->getToPostalCode()) {
            (new Log($this::class))->addLogError(
                Constants::ERRORS['NOT_TO_POINT'] . $this->delivery->getName()
            );
            throw new DeliveryException(DeliveryException::getErrorMessage('NOT_TO_POINT', $this->delivery->getName()));
        }
        if (!$this->delivery->getDimension('weight')) {
            (new Log($this::class))->addLogError(
                Constants::ERRORS['NOT_WEIGHT'] . $this->delivery->getName()
            );
            throw new DeliveryException(DeliveryException::getErrorMessage('NOT_WEIGHT', $this->delivery->getName()));
        }
    }

    /**
     * Ставит результат расчета стоимости доставки
     * @param array|object $result
     * @return void
     */
    public function setResult(array|object $result): void
    {
        $this->result = [new DeliveryResponse(
            [
                'name' => $this->delivery->getName(),
                'description' => $this->delivery->getDescription(),
                'deliverySum' => round($result->getTotalRate()/100),
                'periodMin' => $result->getDeliveryMinDays(),
                'periodMax' => $result->getDeliveryMaxDays()
            ]
        )];
    }

    /**
     * Возвращает результат в виде объекта DeliveryResponse
     * @return array | DeliveryResponse | null
     */
    public function getResult(): array | DeliveryResponse | null
    {
        return $this->result;
    }

    /**
     * Возвращает результат в виде массива
     * @return array|null
     */
    public function getResultToArray(): ?array
    {
        return $this->result ? $this->parseField($this->result) : null;
    }
}