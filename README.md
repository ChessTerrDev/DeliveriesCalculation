
# Расчет стоимости доставок 

> Работа с боевым API возможна только при наличии договоров соответствующих доставок

___
### Список возможностей и содержание:
 - [X] [Получить локацию пользователя по IP адресу](#получить-локацию-пользователя-по-ip-адресу)
 - [X] [Рассчитать стоимость доставки ТК СДЕК](#рассчитать-стоимость-доставки-тк-сдек)
 - [X] [Рассчитать стоимость доставки ТК Почта России](#рассчитать-стоимость-доставки-тк-почта-россии)
 - [X] [Рассчитать стоимость доставки ТК Boxberry](#рассчитать-стоимость-доставки-тк-boxberry)
 - [X] [Рассчитать стоимость доставки ТК OzonRocket](#рассчитать-стоимость-доставки-тк-ozonrocket)
 - [X] [Рассчитать стоимость доставок ВСЕХ ТК](#рассчитать-стоимость-доставок-всех-тк)

## Требования
Автор старался сделать наиболее функциональный и универсальный SDK. Необходимы:
PHP 8.0 и выше, расширение "ext-json" и "ext-soap".

***
## Установка
Установка осуществляется с помощью менеджера пакетов Composer

```bash
composer require chessterrdev/...
```

***
## Руководство к действию

### Ленивая настройка 
> Заполните все необходимые параметры в файле \DeliveriesCalculation\Constants, после реализуйте пример в блоке [Рассчитать стоимость доставок ВСЕХ ТК](#рассчитать-стоимость-доставок-всех-тк)

### Обычная настройка
Система настроена таким образом, чтобы один раз указать авторизационные данные, значения
по умолчанию для всех используемых Транспортных Компаний в файле \DeliveriesCalculation\Constants и в одном месте инициализировать сущность с данными о доставке, 
далее при получении расчета стоимости ТК использовать ранее определенную сущность. 
Подробнее:

___
#### Файл конфигурирования 
> \DeliveriesCalculation\Constants

В файле следует указать авторицационные данные ТК, активность (чтобы отключить ТК укажите 'active' => false), 
название и описание доставки. Так же чтобы в последствии не указывать постоянно при инициализации соответствующей доставки 
повторяющиеся значения, их можно указать здесь же. 
Полный список настроек которые можно использовать и их значения по умолчанию:
```php
    string  name // Обязательно
    string  description = ''
    bool    active // Обязательно 
    string  account = null
    string  secure = null
    string  token = null
    string  key = null
    string  type_client
    float   packagePrice = null
    int     count = 1
    float   estimatedPrice = null
    string  address = null
    string  toPoint = null
    int     toPointId = null
    int     toPostalCode = null
    string  fromPoint = null
    int     fromPointId = null
    int     fromPostalCode = null
```
Какие-то настройки обязательны для одних ТК, какие-то для других, но большинство параметров необязательны.
В файле \DeliveriesCalculation\Constants по умолчанию указанны обязательные.


____
### Получить локацию пользователя по IP адресу
> \DeliveriesCalculation\Location

Локация пользователя определяется по IP Адресу при помощи сервисов https://dadata.ru/api/ или http://ip-api.com.
- ip-api.com бесплатный
- dadata.ru бесплатно до определенного количества запросов в день

По умолчанию при первом запросе данные сохраняются в сессию с ключом 'GEO_DATA' и далее берутся из сессии.
Можно отключить сохранение в сессию указав в константах 'SAVE_IN_SESSION' => false.
> ВАЖНО! Для сохранения в сессию она должна быть открыта, если ваш сервис её не открывает автоматически, это нужно открыть: if (!session_id()) session_start();

Пример:
```php
$location = new DeliveriesCalculation\Location('178.155.5.66');
$result = $location->getGeoData();
```

___
### Информация о посылке
> \DeliveriesCalculation\Entity\Request\Delivery()

Инициализирует сущность, которая принимает в себя все параметры описанные выше, в блоке [Файл конфигурирования](#файл-конфигурирования)
У каждого параметра есть свой get'ер и set'ер. 
Чтобы не указывать отдельно каждый параметр можно отдать массив со значениями в __construct() при инициализации или
воспользоваться методом ->setFields($array).

Примеры:
##### Передать массив с данными при инициализации 
```php
// Получаем массив с данными о локации пользователя, получателя (город, почтовый индекс) по IP адресу
$location = new DeliveriesCalculation\Location('178.155.5.66');

//Создаем сущность с информацией о городе получателя
$deliveryEntity = new \DeliveriesCalculation\Entity\Request\Delivery(
    $location->getGeoData() // Добавляем почтовый индекс получателя в информацию по доставке.
);
```
##### Передать массив с данными через метод ->setFields($array)
```php
// Инициализируем пустую сущность
$deliveryEntity = new \DeliveriesCalculation\Entity\Request\Delivery();

// Добавляем в нее параметры из констант (передаем массив)
$deliveryEntity->setFields(Constants::SDEK_DELIVERY);
```
##### Установить значения через set'еры
```php
// Инициализируем пустую сущность
$deliveryEntity = new \DeliveriesCalculation\Entity\Request\Delivery();

// Добавляем в нее параметры
$deliveryEntity
    ->setToPostalCode(19000) // Почтовый индекс получателя 
    ->setPackagePrice(3658) // Общая стоимость содержимого коробки в рублях
    ->setEstimatedPrice(3656) // Объявленная ценность содержимого коробки.
    ->setDimensions(new Dimensions(1500, 300, 200, 300));
```
##### Получить локацию пользователя и добавить необходимые параметры через set'еры
```php
// Получаем массив с данными о локации пользователя, получателя (город, почтовый индекс) по IP адресу
$location = new DeliveriesCalculation\Location('178.155.5.66');

//Создаем сущность с информацией о городе получателя
$deliveryEntity = new \DeliveriesCalculation\Entity\Request\Delivery(
    $location->getGeoData() // Добавляем почтовый индекс получателя в информацию по доставке.
);

// Добавляем в нее параметры
$deliveryEntity
    ->setAddress('Москва, ул.Октябрьская, 489')
    ->setToPostalCode(19000) // Почтовый индекс получателя 
    ->setPackagePrice(3658) // Общая стоимость содержимого коробки в рублях
    ->setEstimatedPrice(3656) // Объявленная ценность содержимого коробки.
    ->setDimensions(new Dimensions(1500, 300, 200, 300))
    //->setCount(2)
    ;
```


___
### Рассчитать стоимость доставки ТК СДЕК
> \DeliveriesCalculation\Factory\DeliveryFactory::sdekDelivery($DeliveryEntity);

Делаем копию основной информации о доставке которую сформировали ранее, в блоке [Информация о посылке](#информация-о-посылке)
```php
$sdekDeliveryEntity = clone $deliveryEntity;
```
Добавляем (заменяем) авторизационные данные из файла с настройками
```php
$sdekDeliveryEntity->setFields(Constants::SDEK_DELIVERY);
```
Инициализируем клиента доставки
```php
$sdekDelivery = DeliveriesCalculation\Factory\DeliveryFactory::sdekDelivery($sdekDeliveryEntity);
//$sdekDelivery->setToPostalCode(190000);
//$sdekDelivery->setToPointId(1956);
```
Добавляем настройки и считаем доставку
```php
$sdekDelivery
    ->addTariffs([136, 137])
    ->calculation();
```
##### Вернуть результат в виде объекта 
```php
$result = $sdekDelivery->getResult();
```
Вернет массив с тарифами в виде объектов класса \DeliveriesCalculation\Entity\Response\DeliveryResponse
Пример результата:
```
[
  0 => object(DeliveriesCalculation\Entity\Response\DeliveryResponse),
  1 => object(DeliveriesCalculation\Entity\Response\DeliveryResponse),
  2 => object(DeliveriesCalculation\Entity\Response\DeliveryResponse)
]
```
Геттеры объекта \DeliveriesCalculation\Entity\Response\DeliveryResponse:
```
->getName()
->getDescription()
->getCode()
->getDeliverySum()
->getPeriodMin()
->getPeriodMax()
```
##### Вернуть результат в виде массива
```php
$result = $sdekDelivery->getResultToArray();
```
Пример результата:
```
Array ( 
    [0] => Array ( 
        [name] => Посылка склад-дверь 
        [description] => Доставка СДЕК 
        [code] => 137 
        [deliverySum] => 400 
        [periodMin] => 3 
        [periodMax] => 4
    )
    [1] => Array ( 
        [name] => Посылка склад-склад 
        [description] => Доставка СДЕК 
        [code] => 136 
        [deliverySum] => 255 
        [periodMin] => 3 
        [periodMax] => 4 
    )
)
```

___
### Рассчитать стоимость доставки ТК Почта России
> \DeliveriesCalculation\Factory\DeliveryFactory::postalRussiaDelivery($DeliveryEntity);

Делаем копию основной информации о доставке которую сформировали ранее
```php
$postalDeliveryEntity = clone $deliveryEntity;
```
Добавляем (заменяем) авторизационные данные из файла с настройками
```php
$postalDeliveryEntity->setFields(Constants::POSTAL_DELIVERY);
```
Инициализируем клиента доставки
```php
$postalDelivery = DeliveriesCalculation\Factory\DeliveryFactory::postalRussiaDelivery($postalDeliveryEntity);
//$postalDelivery->setToPostalCode(190000);
```
Считаем доставку
```php
$postalDelivery->calculation();
```
##### Вернуть результат в виде объекта
```php
$result = $sdekDelivery->getResult();
```
Вернет массив с тарифами в виде объектов класса \DeliveriesCalculation\Entity\Response\DeliveryResponse
Пример результата:
```
[
  0 => object(DeliveriesCalculation\Entity\Response\DeliveryResponse),
  1 => object(DeliveriesCalculation\Entity\Response\DeliveryResponse),
  2 => object(DeliveriesCalculation\Entity\Response\DeliveryResponse)
]
```
Геттеры объекта \DeliveriesCalculation\Entity\Response\DeliveryResponse:
```
->getName()
->getDescription()
->getCode()
->getDeliverySum()
->getPeriodMin()
->getPeriodMax()
```
##### Вернуть результат в виде массива
```php
$result = $sdekDelivery->getResultToArray();
```
Пример результата:
```
[
    [0] => [ 
        [name] => Доставка Почтой России 
        [description] => Вид РПО - Посылка "нестандартная" / Категория РПО - Обыкновенное 
        [deliverySum] => 373 
        [periodMin] => 0 
        [periodMax] => 3 
    ]
]
```

___
### Рассчитать стоимость доставки ТК Boxberry
> \DeliveriesCalculation\Factory\DeliveryFactory::boxberryDelivery($DeliveryEntity);

Делаем копию основной информации о доставке которую сформировали ранее
```php
$boxberryDeliveryEntity = clone $deliveryEntity;
```
Добавляем (заменяем) авторизационные данные из файла с настройками и ставим пункт назаначения
```php
$boxberryDeliveryEntity
    ->setFields(Constants::BOXBERRY_DELIVERY)
    ->setToPointId(19733);
```
Инициализируем клиента доставки
```php
$boxberryDelivery = DeliveriesCalculation\Factory\DeliveryFactory::boxberryDelivery($boxberryDeliveryEntity);
```
Считаем доставку
```php
$boxberryDelivery->calculation();
```
##### Вернуть результат в виде объекта
```php
$result = $sdekDelivery->getResult();
```
Вернет массив с тарифами в виде объектов класса \DeliveriesCalculation\Entity\Response\DeliveryResponse
Пример результата:
```
[
  0 => object(DeliveriesCalculation\Entity\Response\DeliveryResponse),
  1 => object(DeliveriesCalculation\Entity\Response\DeliveryResponse),
  2 => object(DeliveriesCalculation\Entity\Response\DeliveryResponse)
]
```
Геттеры объекта \DeliveriesCalculation\Entity\Response\DeliveryResponse:
```
->getName()
->getDescription()
->getCode()
->getDeliverySum()
->getPeriodMin()
->getPeriodMax()
```
##### Вернуть результат в виде массива
```php
$result = $sdekDelivery->getResultToArray();
```
Пример результата:
```
[
    [0] => [ 
        [name] => Доставка Boxberry 
        [description] => Доставка Boxberry 
        [deliverySum] => 669 
        [periodMin] => 11 
        [periodMax] => 11 
    ] 
]
```

___
### Рассчитать стоимость доставки ТК OzonRocket
> \DeliveriesCalculation\Factory\DeliveryFactory::ozonRocketDelivery($DeliveryEntity);

Делаем копию основной информации о доставке которую сформировали ранее
```php
$ozonRocketDeliveryEntity = clone $deliveryEntity;
```
Добавляем (заменяем) авторизационные данные из файла с настройками и ставим пункт назаначения
```php
$ozonRocketDeliveryEntity
    ->setFields(Constants::OZON_ROCKET_DELIVERY)
    ->setAddress('Мовапвапсква');
```
Инициализируем клиента доставки
```php
$ozonRocketDelivery = DeliveriesCalculation\Factory\DeliveryFactory::ozonRocketDelivery($ozonRocketDeliveryEntity);
```
Считаем доставку
```php
$ozonRocketDelivery->calculation();
```
##### Вернуть результат в виде объекта
```php
$result = $sdekDelivery->getResult();
```
Вернет массив с тарифами в виде объектов класса \DeliveriesCalculation\Entity\Response\DeliveryResponse
Пример результата:
```
[
  0 => object(DeliveriesCalculation\Entity\Response\DeliveryResponse),
  1 => object(DeliveriesCalculation\Entity\Response\DeliveryResponse),
  2 => object(DeliveriesCalculation\Entity\Response\DeliveryResponse)
]
```
Геттеры объекта \DeliveriesCalculation\Entity\Response\DeliveryResponse:
```
->getName()
->getDescription()
->getCode()
->getDeliverySum()
->getPeriodMin()
->getPeriodMax()
```
##### Вернуть результат в виде массива
```php
$result = $sdekDelivery->getResultToArray();
```
Пример результата:
```
[
    [0] => [ 
        [name] => Доставка OzonRocket: ExpressCourier 
        [description] => Доставка OzonRocket 
        [deliverySum] => 600 
        [periodMin] => 4 
        [periodMax] => 4 
    ]
    [1] => [ 
        [name] => Доставка OzonRocket: Courier 
        [description] => Доставка OzonRocket 
        [deliverySum] => 500 
        [periodMin] => 42 
        [periodMax] => 42 
    ]
]
```

___
### Рассчитать стоимость доставок ВСЕХ ТК
Это не отдельный метод, это просто упрощение предыдущего описания 
```php
$location = new DeliveriesCalculation\Location('178.155.5.66');
$deliveryEntity = new \DeliveriesCalculation\Entity\Request\Delivery($location->getGeoData());
$deliveryEntity
    ->setPackagePrice(3658) // Общая стоимость содержимого коробки в рублях
    ->setEstimatedPrice(3656) // Объявленная ценность содержимого коробки.
    ->setDimensions(new Dimensions(1500, 300, 200, 300));


$sdekDeliveryEntity = clone $deliveryEntity;
$sdekDeliveryEntity->setFields(Constants::SDEK_DELIVERY);
$sdekDelivery = DeliveriesCalculation\Factory\DeliveryFactory::sdekDelivery($sdekDeliveryEntity);
$sdekResult = $sdekDelivery
    ->addTariffs([136, 137])
    ->calculation()
    ->getResultToArray();


$postalDeliveryEntity = clone $deliveryEntity;
$postalDeliveryEntity->setFields(Constants::POSTAL_DELIVERY);
$postalDelivery = DeliveriesCalculation\Factory\DeliveryFactory::postalRussiaDelivery($postalDeliveryEntity);
$postaResult = $postalDelivery
    ->calculation()
    ->getResultToArray();


$boxberryDeliveryEntity = clone $deliveryEntity;
$boxberryDeliveryEntity
    ->setFields(Constants::BOXBERRY_DELIVERY)
    ->setToPointId(19733);
$boxberryDelivery = DeliveriesCalculation\Factory\DeliveryFactory::boxberryDelivery($boxberryDeliveryEntity);
$boxberryResult = $boxberryDelivery
    ->calculation()
    ->getResultToArray();


$ozonRocketDeliveryEntity = clone $deliveryEntity;
$ozonRocketDeliveryEntity
    ->setFields(Constants::OZON_ROCKET_DELIVERY)
    ->setAddress('Мовапвапсква');
$ozonRocketDelivery = DeliveriesCalculation\Factory\DeliveryFactory::ozonRocketDelivery($ozonRocketDeliveryEntity);
$ozonResult = $ozonRocketDelivery
    ->calculation()
    ->getResultToArray();
```
Результат:
```
var_dump($sdekResult, $postaResult, $boxberryResult, $ozonResult);
```



