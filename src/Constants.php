<?php

namespace DeliveriesCalculation;

class Constants
{
    /**
     * Авторизационные данные для получения локации пользователя
     */
    public const LOCATION = [
        'dadata' => [
            'token' 	=> 'example_example_example_example_example',
            'secret'	=> 'example_example_example_example_example'
        ]
    ];

    /**
     * Настройки для доставки СДЕК
     */
    public const SDEK_DELIVERY = [
        'active' => true,
        'name' => 'Доставка СДЕК',
        'description' => 'Доставка СДЕК',
        'account' => 'example_example_example_example_example',
        'secure' => 'example_example_example_example_example',
        'fromPoint' => null,
        'fromPointId' => null,
        'fromPostalCode' => 190000
    ];

    /**
     * Настройки для доставки "Почта России"
     */
    public const POSTAL_DELIVERY = [
        'active' => true,
        'name' => 'Доставка Почтой России',
        'description' => 'Вид РПО - Посылка "нестандартная" / Категория РПО - Обыкновенное',
        'account' => 'Sexample_example_example_example_example',
        'secure' => 'example_example_example_example_example',
        'token' => 'example_example_example_example_example',
        'key' => 'example_example_example_example_example',
        'fromPostalCode' => 190000
    ];

    /**
     * Настройки для доставки "BOXBERRY"
     */
    public const BOXBERRY_DELIVERY = [
        'active' => true,
        'name' => 'Доставка Boxberry',
        'description' => 'Доставка Boxberry',
        'token' => 'example_example_example_example_example',
        'fromPointId' => 010
    ];

    /**
     * Настройки для доставки "OzonRocket"
     */
    public const OZON_ROCKET_DELIVERY = [
        'active' => true,
        'name' => 'Доставка OzonRocket',
        'description' => 'Доставка OzonRocket',
        'account' => null,
        'secure' => null,
        'fromPointId' => 15
    ];

    /**
     * Описание ошибок на языке "RU"
     */
    public const ERRORS = [
        'NOT_FROM_POINT' => 'Не известен пункт назначения посылки в системе доставки: ',
        'NOT_TO_POINT' => 'Не известен пункт отправления посылки в системе доставки: ',
        'NOT_WEIGHT' => 'Не известен вес посылки в системе доставки: ',
        'NOT_PRICE' => 'Не известна объявленная ценность содержимого коробки в системе доставки: ',
        'NOT_ADDRESS' => 'Не известен адрес доставки посылки в системе доставки: ',
        'NOT_DIMENSIONS' => 'Не указанны габариты отправления в системе доставки: ',
        'ERROR_RESPONSE' => 'Получен некорректный ответ в системе доставки: '
    ];

    /**
     * Пути к файлам логов
     */
    public const LOG_FILES_PATH = [
        'EMERGENCY'  => __DIR__ . '/../logs/EMERGENCY.log',  // System is unusable.
        'ALERT'      => __DIR__ . '/../logs/ALERT.log',      // Action must be taken immediately. Example: Entire website down, database unavailable, etc.
        'CRITICAL'   => __DIR__ . '/../logs/CRITICAL.log',   // Critical conditions. Example: Application component unavailable, unexpected exception.
        'ERROR'      => __DIR__ . '/../logs/ERROR.log',      // Runtime errors that do not require immediate action but should typically monitored.
        'WARNING'    => __DIR__ . '/../logs/WARNING.log',    // Exceptional occurrences that are not errors.Example: Use of deprecated APIs.
        'NOTICE'     => __DIR__ . '/../logs/NOTICE.log',     // Normal but significant events.
        'INFO'       => __DIR__ . '/../logs/INFO.log',       // Interesting events. Example: User logs in, SQL logs.
        'DEBUG'      => __DIR__ . '/../logs/DEBUG.log',      // Detailed debug information.
    ];

    /**
     * Описание сообщений для логов на языке "RU"
     */
    public const LOG_MESSAGE = [
        'NO_ACTIVE' => 'Доставка отключена в конфигурационном файле!',
        'NO_RESULT' => 'Пришел пустой результат в системе доставки: '
    ];


}