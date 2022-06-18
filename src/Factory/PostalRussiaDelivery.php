<?php

namespace DeliveriesCalculation\Factory;

use LapayGroup\RussianPost\{Providers\OtpravkaApi, ParcelInfo};
use DeliveriesCalculation\{
    Constants,
    Exception\DeliveryException,
    Entity\Delivery,
    Entity\DeliveryResponse
};

class PostalRussiaDelivery implements DeliveryInterface
{
    private OtpravkaApi $client;

    /**
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
     * При удачном исходе возвращает стоимость доставки
     * @param bool $typeReturnValue тип возвращаемого значения:
     * bool false = Array |
     * bool true = DeliveryResponse (по умолчанию)
     * @return array | object | null
     * @throws \DeliveriesCalculation\Exception\DeliveryException
     */
    public function getDeliveryCalculation(bool $typeReturnValue = true): array | DeliveryResponse | null
    {
        if (!$this->delivery->isActive()) return null;
        if (!$this->delivery->getFromPostalCode())
            throw new DeliveryException(DeliveryException::getErrorMessage('NOT_FROM_POINT', Constants::POSTAL_DELIVERY['name']));
        if (!$this->delivery->getToPostalCode())
            throw new DeliveryException(DeliveryException::getErrorMessage('NOT_TO_POINT', Constants::POSTAL_DELIVERY['name']));
        if (!$this->delivery->getDimension('weight'))
            throw new DeliveryException(DeliveryException::getErrorMessage('NOT_WEIGHT', Constants::POSTAL_DELIVERY['name']));

        $parcelInfo = new ParcelInfo();
        $parcelInfo->setIndexFrom($this->delivery->getFromPostalCode()); // Индекс пункта сдачи из функции $OtpravkaApi->shippingPoints()
        $parcelInfo->setIndexTo($this->delivery->getToPostalCode());
        $parcelInfo->setMailCategory('ORDINARY'); // https://otpravka.pochta.ru/specification#/enums-base-mail-category
        $parcelInfo->setMailType('POSTAL_PARCEL'); // https://otpravka.pochta.ru/specification#/enums-base-mail-type
        $parcelInfo->setWeight($this->delivery->getDimension('weight'));
        $parcelInfo->setFragile(true);

        $result =  $this->client->getDeliveryTariff($parcelInfo);

        if ($typeReturnValue) {
            return new DeliveryResponse(
                [
                    'name' => Constants::POSTAL_DELIVERY['name'],
                    'description' => Constants::POSTAL_DELIVERY['description'],
                    'delivery_sum' => round($result->getTotalRate()/100),
                    'period_min' => $result->getDeliveryMinDays(),
                    'period_max' => $result->getDeliveryMaxDays()
                ]
            );
        } else {
            return [
                'name' => Constants::POSTAL_DELIVERY['name'],
                'description' => Constants::POSTAL_DELIVERY['description'],
                'delivery_sum' => round($result->getTotalRate()/100),
                'period_min' => $result->getDeliveryMinDays(),
                'period_max' => $result->getDeliveryMaxDays()
            ];
        }
    }
}