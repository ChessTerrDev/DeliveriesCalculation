<?php

namespace DeliveriesCalculation\Logger;

use Monolog\{Logger, Handler\StreamHandler};
use DeliveriesCalculation\Constants;


class Log
{
    private object $log;

    public function __construct($name)
    {
        $this->log = $log = new Logger($name);

        $log->pushHandler(new StreamHandler(Constants::LOG_FILES_PATH['EMERGENCY'], Logger::EMERGENCY));
        $log->pushHandler(new StreamHandler(Constants::LOG_FILES_PATH['ALERT'], Logger::ALERT));
        $log->pushHandler(new StreamHandler(Constants::LOG_FILES_PATH['CRITICAL'], Logger::CRITICAL));
        $log->pushHandler(new StreamHandler(Constants::LOG_FILES_PATH['ERROR'], Logger::ERROR));
        $log->pushHandler(new StreamHandler(Constants::LOG_FILES_PATH['WARNING'], Logger::WARNING));
        $log->pushHandler(new StreamHandler(Constants::LOG_FILES_PATH['NOTICE'], Logger::NOTICE));
        $log->pushHandler(new StreamHandler(Constants::LOG_FILES_PATH['INFO'], Logger::INFO));
        $log->pushHandler(new StreamHandler(Constants::LOG_FILES_PATH['DEBUG'], Logger::DEBUG));
    }

    public function addLogEmergency($message, $context = [])
    {
        $this->log->emergency($message, $context);
    }

    public function addLogAlert($message, $context = [])
    {
        $this->log->alert($message, $context);
    }

    public function addLogCritical($message, $context = [])
    {
        $this->log->critical($message, $context);
    }

    public function addLogError($message, $context = [])
    {
        $this->log->error($message, $context);
    }

    public function addLogWarning($message, $context = [])
    {
        $this->log->warning($message, $context);
    }

    public function addLogNotice($message, $context = [])
    {
        $this->log->notice($message, $context);
    }

    public function addLogInfo($message, $context = [])
    {
        $this->log->info($message, $context);
    }

    public function addLogDebug($message, $context = [])
    {
        $this->log->debug($message, $context);
    }





}