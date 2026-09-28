<?php

namespace Improntus\PowerPay\Logger\Handler;

use Magento\Framework\Logger\Handler\Base;
use Monolog\Logger;

class Error extends Base
{
    /**
     * @var int
     */
    protected $loggerType = Logger::ERROR;

    /**
     * @var string
     */
    protected $fileName = 'var/log/powerpay/error.log';
}
