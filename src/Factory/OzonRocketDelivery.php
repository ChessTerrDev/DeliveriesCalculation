<?php

namespace DeliveriesCalculation\Factory;

use DeliveriesCalculation\{
    Constants,
    Entity\Response\DeliveryResponse,
    Entity\Request\Delivery,
    Exception\DeliveryException,
    Logger\Log
};
use OzonRocketSDK\{
    Client\Client as OzonClient,
    Entity\Common\Dimensions as OzonDimensions,
    Entity\Request\DeliveryCalculateInformation as OzonDelivery,
    Entity\Common\Package as OzonPackage
};
use GuzzleHttp\Exception\GuzzleException;

class OzonRocketDelivery extends AbstractDelivery implements DeliveryInterface
{

    /**
     * Инициализирует клиента OzonRocketSDK
     * @param Delivery $delivery Вся информация о посылке, откуда, куда, сколько, как и т.д.
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function __construct(Delivery $delivery)
    {
        $this->delivery = $delivery;
        if ($this->delivery->isActive())
        {
            try {
                if ($this->delivery->getAccount() && $this->delivery->getSecure()) {
                    $this->client = new OzonClient(
                        $this->delivery->getAccount(),
                        $this->delivery->getSecure()
                    );
                } else {
                    $this->client = new OzonClient('TEST');
                }
            } catch (\Exception $e) {
                (new Log($this::class))->addLogError(
                    'Не удалось авторизоваться в системе доставки: ' . $this->delivery->getName(),
                    (array)$e
                );
            }
        }
    }

    /**
     * При удачном исходе получает стоимость доставки
     * @return \DeliveriesCalculation\Factory\DeliveryInterface
     * @throws \DeliveriesCalculation\Exception\DeliveryException
     */
    public function calculation(): DeliveryInterface
    {
        if (!$this->delivery->isActive()) {
            (new Log($this::class))->addLogInfo(
                Constants::LOG_MESSAGE['NO_ACTIVE'] . $this->delivery->getName()
            );
            return $this;
        }
        $this->checkParameters();

        // Информация о грузовом месте (отправлении).
        $package = (new OzonPackage())
            ->setCount($this->delivery->getCount()) // Количество одинаковых коробок.
            // Информация о габаритах. (вес в гр / Длинна в мм / Высота в мм / Ширина в мм)
            ->setDimensions(new OzonDimensions(
                    $this->delivery->getDimension('weight'),
                    $this->delivery->getDimension('length'),
                    $this->delivery->getDimension('height'),
                    $this->delivery->getDimension('width')
                )
            )
            ->setPrice(round($this->delivery->getPackagePrice(), 0)) // Общая стоимость содержимого коробки в рублях.
            ->setEstimatedPrice(round($this->delivery->getEstimatedPrice(), 0)); // Объявленная ценность содержимого коробки.

        $ozonDelivery = new OzonDelivery(
            $this->delivery->getFromPointId(), // Идентификатор места отправления. Значения id можно получить из ответа метода $client->deliveryFromPlaces()
            $this->delivery->getAddress(), // Адрес доставки.
            [$package] // Массив информации по отправлениям.
        );

        try {
            $result = $this->client->deliveryCalculateInformation($ozonDelivery);

            /*$result =  [
                "deliveryInfos" => [
                    [
                        "deliveryType" => "ExpressCourier",
                        "price" => 600,
                        "pricePositions" => [
                            [
                                "type" => "Delivery",
                                "amount" => 600
                            ]
                        ],
                        "deliveryTermInDays" => 4,
                        "isAviaDeliveryVariant" => true
                    ],
                    [
                        "deliveryType" => "Courier",
                        "price" => 500,
                        "pricePositions" => [
                            [
                                "type" => "Delivery",
                                "amount" => 500
                            ]
                        ],
                        "deliveryTermInDays" => 42,
                        "isAviaDeliveryVariant" => true
                    ]
                ]
            ];*/

            if (isset($result['deliveryInfos']) && !empty($result['deliveryInfos'])) {

                $this->setResult($result['deliveryInfos']);

            } elseif (isset($result['deliveryInfos'])) {

                (new Log($this::class))->addLogInfo(
                    Constants::LOG_MESSAGE['NO_RESULT'] . $this->delivery->getName(),
                    $ozonDelivery->getFields()
                );
            }

        } catch (GuzzleException|\Exception $e) {
            (new Log($this::class))->addLogError(
                Constants::ERRORS['ERROR_RESPONSE'] . $this->delivery->getName(),
                (array)$e
            );
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
        if (!$this->delivery->getFromPointId()) {
            (new Log($this::class))->addLogError(
                Constants::ERRORS['NOT_FROM_POINT'] . $this->delivery->getName()
            );
            throw new DeliveryException(DeliveryException::getErrorMessage('NOT_FROM_POINT', $this->delivery->getName()));
        }
        if (!$this->delivery->getAddress()) {
            (new Log($this::class))->addLogError(
                Constants::ERRORS['NOT_ADDRESS'] . $this->delivery->getName()
            );
            throw new DeliveryException(DeliveryException::getErrorMessage('NOT_ADDRESS', $this->delivery->getName()));
        }
        if (!$this->delivery->getEstimatedPrice() || !$this->delivery->getPackagePrice()) {
            (new Log($this::class))->addLogError(
                Constants::ERRORS['NOT_PRICE'] . $this->delivery->getName()
            );
            throw new DeliveryException(DeliveryException::getErrorMessage('NOT_PRICE', $this->delivery->getName()));
        }
        if (!$this->delivery->getDimension('weight')) {
            (new Log($this::class))->addLogError(
                Constants::ERRORS['NOT_WEIGHT'] . $this->delivery->getName()
            );
            throw new DeliveryException(DeliveryException::getErrorMessage('NOT_WEIGHT', $this->delivery->getName()));
        }
        if (!$this->delivery->getDimension('length') || !$this->delivery->getDimension('height') || !$this->delivery->getDimension('width')) {
            (new Log($this::class))->addLogError(
                Constants::ERRORS['NOT_DIMENSIONS'] . $this->delivery->getName()
            );
            throw new DeliveryException(DeliveryException::getErrorMessage('NOT_DIMENSIONS', $this->delivery->getName()));
        }
    }

    /**
     * Ставит результат расчета стоимости доставки
     * @param object|array $result
     * @return void
     */
    public function setResult(object|array $result): void
    {
        $tariffs = [];
        foreach ($result as $tariff) $tariffs[$tariff['deliveryType']] = new DeliveryResponse(
            [
                'name' => $this->delivery->getName() . ': ' . $tariff['deliveryType'],
                'description' => $this->delivery->getDescription(),
                'deliverySum' => round($tariff['price']),
                'periodMin' => $tariff['deliveryTermInDays'],
                'periodMax' => $tariff['deliveryTermInDays']
            ]);

        $this->result = $tariffs;
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