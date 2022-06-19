<?php

namespace DeliveriesCalculation;

use DeliveriesCalculation\Logger\Log;

class Session
{

    /**
     * Метод записи в сессию
     * @param $value = значение которое пишется в массив на имени $name
     * если в $value передан массив в сессию пишется все по ключу значению
     */
    public static function addToSession($value, $name = false): bool
    {
        if(empty($value)) return false;
        if(!session_id()) session_start();

        if (is_array($value) && !$name)
        {
            foreach ($value as $key => $val)
            {
                $_SESSION[(string)$key] = $val;
            }

        } else {

            $name = (string)$name;
            if(isset($_SESSION[$name]))
            {
                $_SESSION[$name] = array_merge($_SESSION[$name], (array)$value);

            } else {

                $_SESSION[$name] = $value;
            }
        }
        (new Log(self::class))->addLogInfo('GEO_DATA добавленна в сессию.', $value);
        return true;
    }

    /**
     * Возвращает значение по первому найденному ключу в сессии
     * @param string $name
     * @return mixed|null
     */
    public static function getFromSession(string $name)
    {
        (new Log(self::class))->addLogInfo('Из сессии получено: ' . $name);
        return self::search($_SESSION, $name);
    }

    /**
     * Возвращает значение по первому найденному ключу в многомерном массиве
     * @param array $array
     * @param $key
     * @return mixed|null
     */
    public static function search(array $array, $key,)
    {
        if (isset($array[$key])) return $array[$key];

        foreach ($array as $value) if (is_array($value)) return self::search($value, $key);

        return null;
    }

}