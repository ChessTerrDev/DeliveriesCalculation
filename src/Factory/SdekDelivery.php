<?php

namespace DeliveriesCalculation\Factory;

use AntistressStore\CdekSDK2\CdekClientV2;
use DeliveriesCalculation\{Constants,
    Exception\DeliveryException,
    Entity\Delivery,
    Entity\DeliveryResponse,
    Logger\Log};



class SdekDelivery extends AbstractDelivery implements DeliveryInterface
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


    public function calculation(): SdekDelivery
    {
        if (!$this->delivery->isActive()) {
            (new Log($this::class))->addLogInfo(
                Constants::SDEK_DELIVERY['name'] . ': ' . Constants::LOG_MESSAGE['NO_ACTIVE']
            );
            return $this;
        }
        $this->checkParameters();

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
        $tariff->setPackageWeight($this->delivery->getDimension('weight'));

        $tariff
            ->setType($this->type) // 1 - "интернет-магазин", 2 - "доставка"
            ->addServices(['PART_DELIV']) //список сервис кодов -  \AntistressStore\CdekSDK2\Constants - SERVICE_CODES
        ;

        // Получаем стоимость доставки для всех тарифов
        $tariffList = $this->client->calculateTariffList($tariff);
        $this->setResult($tariffList);


        return $this;
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


    private function checkParameters(): void
    {
        if (!$this->delivery->getFromPointId() and !$this->delivery->getFromPostalCode()) {
            (new Log($this::class))->addLogError(
                Constants::SDEK_DELIVERY['name'] . ': ' . Constants::ERRORS['NOT_FROM_POINT']
            );
            throw new DeliveryException(DeliveryException::getErrorMessage('NOT_FROM_POINT', Constants::SDEK_DELIVERY['name']));
        }
        if (!$this->delivery->getToPointId() and !$this->delivery->getToPostalCode()) {
            (new Log($this::class))->addLogError(
                Constants::SDEK_DELIVERY['name'] . ': ' . Constants::ERRORS['NOT_FROM_POINT']
            );
            throw new DeliveryException(DeliveryException::getErrorMessage('NOT_TO_POINT', Constants::SDEK_DELIVERY['name']));
        }
        if (!$this->delivery->getDimension('weight')) {
            (new Log($this::class))->addLogError(
                Constants::SDEK_DELIVERY['name'] . ': ' . Constants::ERRORS['NOT_FROM_POINT']
            );
            throw new DeliveryException(DeliveryException::getErrorMessage('NOT_WEIGHT', Constants::SDEK_DELIVERY['name']));
        }
    }

    /**
     * Ставит результат расчета стоимости доставки
     * @param array|object $result
     * @return void
     */
    public function setResult(array|object $result): void
    {
        $tariffs = [];
        foreach ($result as $tariff)
        {
            if (!empty($this->tariffs) && in_array($tariff->getTariffCode(), $this->tariffs))
            {
                // Если указанны id нужных тарифов, отдаем только их.
                $tariffs[$tariff->getTariffCode()] = $this->setTariffResult($tariff);

            } elseif(empty($this->tariffs)) {

                $tariffs[$tariff->getTariffCode()] = $this->setTariffResult($tariff);
            }
        }
        $this->result = $tariffs;

    }

    /**
     * @param \AntistressStore\CdekSDK2\Entity\Responses\TariffListResponse $tariff
     * @param array $tariffs
     * @return array
     */
    private function setTariffResult(\AntistressStore\CdekSDK2\Entity\Responses\TariffListResponse $tariff): DeliveryResponse
    {
        return new DeliveryResponse(
            [
                'name' => $tariff->getTariffName(),
                'description' => $tariff->getTariffDescription(),
                'code' => $tariff->getTariffCode(),
                'deliverySum' => round($tariff->getDeliverySum()),
                'periodMin' => $tariff->getPeriodMin(),
                'periodMax' => $tariff->getPeriodMax()
            ]
        );
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
        return $this->parseField($this->result);
    }
}