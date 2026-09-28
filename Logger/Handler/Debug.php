<?php

namespace Improntus\PowerPay\Logger\Handler;

use Magento\Framework\Logger\Handler\Base;
use Monolog\Logger;

class Debug extends Base
{
    /**
     * @var int
     */
    protected $loggerType = Logger::DEBUG;

    /**
     * @var string
     */
    protected $fileName = 'var/log/powerpay/debug.log';
}
