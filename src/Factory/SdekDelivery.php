<?php

namespace DeliveriesCalculation\Factory;

use AntistressStore\CdekSDK2\CdekClientV2;
use DeliveriesCalculation\{
    Constants,
    Exception\DeliveryException,
    Entity\Delivery,
    Entity\DeliveryResponse
};
use Monolog\{Logger, Handler\StreamHandler};


class SdekDelivery implements DeliveryInterface
{
    /**
     * @var int 1 - "интернет-магазин", 2 - "доставка"
     */
    private int $type = 1;

    /**
     * @var array
     */
    private array $tariffs = [];

    /**
     * @var bool array = false | object = true
     */
    private bool $typeReturnValue;

    /**
     * Вся информация о посылке, откуда, куда, сколько, как и т.д.
     * @var \DeliveriesCalculation\Entity\Delivery
     */
    private Delivery     $delivery;

    /**
     * @var \AntistressStore\CdekSDK2\CdekClientV2
     */
    private CdekClientV2 $client;

    /**
     * @param \DeliveriesCalculation\Entity\Delivery $delivery Вся информация о посылке, откуда, куда, сколько, как и т.д.
     */
    public function __construct(Delivery $delivery)
    {
        $this->delivery = $delivery;
        if ($this->delivery->isActive()) {
            $this->client = $this->delivery->getAccount() == 'TEST' ?
                new CdekClientV2($this->delivery->getAccount()) :
                new CdekClientV2($this->delivery->getAccount(), $this->delivery->getSecure());
        }
    }

    /**
     * При удачном исходе возвращает стоимость доставки
     * @param bool $typeReturnValue тип возвращаемого значения:
     * array = false |
     * \DeliveriesCalculation\Entity\DeliveryResponse = true (по умолчанию)
     * @return array | DeliveryResponse | null
     * @throws \Exception
     */
    public function getDeliveryCalculation(bool $typeReturnValue = true): array | DeliveryResponse | null
    {
        if (!$this->delivery->isActive()) return null;
        if (!$this->delivery->getFromPointId() and !$this->delivery->getFromPostalCode())
            throw new DeliveryException(DeliveryException::getErrorMessage('NOT_FROM_POINT', Constants::SDEK_DELIVERY['name']));
        if (!$this->delivery->getToPointId() and !$this->delivery->getToPostalCode())
            throw new DeliveryException(DeliveryException::getErrorMessage('NOT_TO_POINT', Constants::SDEK_DELIVERY['name']));
        if (!$this->delivery->getDimension('weight'))
            throw new DeliveryException(DeliveryException::getErrorMessage('NOT_WEIGHT', Constants::SDEK_DELIVERY['name']));

        $this->typeReturnValue = $typeReturnValue;
        $tariff 		= new \AntistressStore\CdekSDK2\Entity\Requests\Tariff();
        $Location 		= new \AntistressStore\CdekSDK2\Entity\Requests\Location();

        // Ставим пункт отправления
        $tariff->setFromLocation(
            $this->delivery->getToPointId() ?
            $Location->withCode($this->delivery->getFromPointId()) :
            $Location->withPostalCode($this->delivery->getFromPostalCode())
        );
        // Ставим пункт назначения
        $tariff->setToLocation(
            $this->delivery->getFromPointId() ?
            $Location->withCode($this->delivery->getToPointId()) :
            $Location->withPostalCode($this->delivery->getToPostalCode())
        );

        // Ставим вес посылки
        if ($this->delivery->getDimension('weight'))
            $tariff
                ->setPackageWeight($this->delivery->getDimension('weight'));

        $tariff
            ->setType($this->type) // 1 - "интернет-магазин", 2 - "доставка"
            ->addServices(['PART_DELIV']) //список сервис кодов -  \AntistressStore\CdekSDK2\Constants - SERVICE_CODES
        ;

        // Получаем стоимость доставки для всех тарифов
        $tariffList = $this->client->calculateTariffList($tariff);
        $_aTariffs = [];
        foreach ($tariffList as $tariff)
        {
            if (!empty($this->tariffs) && in_array($tariff->getTariffCode(), $this->tariffs))
            {
                // Если указанны id нужных тарифов, отдаем только их.
                $_aTariffs = $this->setTariffResult($tariff, $_aTariffs);

            } elseif(empty($this->tariffs)) {

                $_aTariffs = $this->setTariffResult($tariff, $_aTariffs);
            }
        }
        return $_aTariffs;

    }

    /**
     * @param array $tariffs
     * @return SdekDelivery
     */
    public function addTariffs(array $tariffs): SdekDelivery
    {
        $this->tariffs = [...$this->tariffs, ...$tariffs];
        return $this;
    }

    /**
     * @param \AntistressStore\CdekSDK2\Entity\Responses\TariffListResponse $tariff
     * @param array $_aTariffs
     * @return array
     */
    private function setTariffResult(\AntistressStore\CdekSDK2\Entity\Responses\TariffListResponse $tariff, array $_aTariffs): array
    {
        if ($this->typeReturnValue) {
            $_aTariffs[$tariff->getTariffCode()] = new DeliveryResponse(
                [
                    'name' => $tariff->getTariffName(),
                    'description' => $tariff->getTariffDescription(),
                    'code' => $tariff->getTariffCode(),
                    'delivery_sum' => round($tariff->getDeliverySum()),
                    'period_min' => $tariff->getPeriodMin(),
                    'period_max' => $tariff->getPeriodMax()
                ]
            );
        } else {
            $_aTariffs[$tariff->getTariffCode()]['tariff_name'] = $tariff->getTariffName();
            $_aTariffs[$tariff->getTariffCode()]['tariff_description'] = $tariff->getTariffDescription();
            $_aTariffs[$tariff->getTariffCode()]['tariff_code'] = $tariff->getTariffCode();
            $_aTariffs[$tariff->getTariffCode()]['delivery_sum'] = round($tariff->getDeliverySum());
            $_aTariffs[$tariff->getTariffCode()]['deliveryMinDays'] = $tariff->getPeriodMin();
            $_aTariffs[$tariff->getTariffCode()]['deliveryMaxDays'] = $tariff->getPeriodMax();
        }
        return $_aTariffs;
    }

    /**
     * @return array
     */
    public function getTariffs(): array
    {
        return $this->tariffs;
    }
}