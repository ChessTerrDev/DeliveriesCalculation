<?php

namespace DeliveriesCalculation\Exception;

use DeliveriesCalculation\Constants;

class DeliveryException extends \Exception
{

    public static function getErrorMessage($code, $name)
    {
        if (array_key_exists($code, Constants::ERRORS)) {
            return Constants::ERRORS[$code].'. '.$name;
        }

        return $name;
    }
}