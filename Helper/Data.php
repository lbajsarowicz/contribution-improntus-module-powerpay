<?php

namespace Improntus\PowerPay\Helper;

use Magento\Framework\Encryption\EncryptorInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Sales\Model\Order;
use Magento\Store\Model\StoreManagerInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;

class Data
{
    private const CONFIG_ROOT = 'payment/powerpay/';
    public const CLIENTID = 'clientid';
    public const SECRET = 'secret';
    public const SANDBOX = 'sandbox';
    public const USER_AUTHENTICATED = 1;
    public const INCOMPLETE_CREDENTIALS = 0;
    public const ACTIVE = 'active';
    public const TITLE = 'title';
    public const DEBUG = 'debug';
    public const MASK_AUTH_TOKEN = 'mask_auth_token';
    public const MERCHANT_ID = 'merchant_id';
    public const CONCEPT = 'concept';
    public const CANCEL_HOURS = 'cancel_hours';
    public const WIDGETS_ENABLED = 'widgets';
    public const PRODUCT_WIDGET = 'product_widget';
    public const HEADER_WIDGET = 'header_widget';
    public const BANNER_WIDGET = 'banner_widget';
    public const CHECKOUT_WIDGET = 'checkout_widget';
    public const CUSTOM_SUCCESS = 'custom_success';
    public const EP_MERCHANT_TRANSACTIONS = 'merchant-transactions';

    /* PDP widget modes (payment/powerpay/product_widget) */
    public const PRODUCT_WIDGET_DISABLED = 0;
    public const PRODUCT_WIDGET_DEFAULT = 1;
    public const PRODUCT_WIDGET_NEUTRAL = 2;

    private const LOG_LEVELS = [
        'critical' => LogLevel::CRITICAL,
        'error' => LogLevel::ERROR,
        'warning' => LogLevel::WARNING,
        'info' => LogLevel::INFO,
        'debug' => LogLevel::DEBUG,
    ];

    /**
     * @var LoggerInterface
     */
    private $debugLogger;

    /**
     * @var LoggerInterface
     */
    private $errorLogger;

    /**
     * @var EncryptorInterface
     */
    private $encryptor;

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     *
     * /**
     * @param EncryptorInterface $encryptor
     * @param ScopeConfigInterface $scopeConfig
     * @param LoggerInterface $debugLogger
     * @param LoggerInterface $errorLogger
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        EncryptorInterface $encryptor,
        ScopeConfigInterface $scopeConfig,
        LoggerInterface $debugLogger,
        LoggerInterface $errorLogger,
        StoreManagerInterface $storeManager
    )
    {
        $this->storeManager = $storeManager;
        $this->debugLogger = $debugLogger;
        $this->errorLogger = $errorLogger;
        $this->encryptor = $encryptor;
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * @param $value
     * @param $storeId
     * @return mixed|string
     */
    public function getConfigData($value, $storeId = null)
    {
        $path = self::CONFIG_ROOT . $value;
        /* client_id and secret must be decrypted after retrieved */
        if ($value === self::CLIENTID || $value === self::SECRET) {
            return $this->encryptor->decrypt($this->scopeConfig->getValue($path, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId) ?? '');
        }
        return $this->scopeConfig->getValue($path, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId) ?? '';
    }

    /**
     * @param $path
     * @param $params
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    private function getUrl($path, $params = null)
    {
        if ($params) {
            return $this->storeManager->getStore()->getUrl($path, $params);
        }
        return $this->storeManager->getStore()->getUrl($path);
    }

    /**
     * @return int
     */
    public function validateCredentials()
    {
        $result = self::INCOMPLETE_CREDENTIALS;
        if ($this->getConfigData(self::CLIENTID) && $this->getConfigData(self::SECRET)) {
            $result = self::USER_AUTHENTICATED;
        }
        return $result;
    }

    /**
     * @return bool
     */
    public function isActive()
    {
        return (bool)$this->getConfigData(self::ACTIVE);
    }

    /**
     * @return mixed|string
     */
    public function getTitle($storeId = null)
    {
        return $this->getConfigData(self::TITLE, $storeId);
    }

    /**
     * @return mixed|string
     */
    public function getSecret($storeId = null)
    {
        return $this->getConfigData(self::SECRET, $storeId);
    }

    /**
     * @return mixed|string
     */
    public function getClientId($storeId = null)
    {
        return $this->getConfigData(self::CLIENTID, $storeId);
    }

    /**
     * @param $storeId
     * @return bool
     */
    public function isDebugEnabled($storeId = null)
    {
        return (bool)$this->getConfigData(self::DEBUG, $storeId);
    }

    /**
     * @param $storeId
     * @return bool
     */
    public function isAuthTokenMasked($storeId = null)
    {
        return (bool)$this->getConfigData(self::MASK_AUTH_TOKEN, $storeId);
    }

    /**
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getRedirectUrl()
    {
        return $this->getUrl('powerpay/order/create');
    }
    /**
     * @param $token
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getCallBackUrl($token = null)
    {
        if ($token)
        {
            return $this->getUrl('powerpay/order/response', ['token' => $token]);
        }
        return $this->getUrl('powerpay/order/response');
    }

    /**
     * @param $storeId
     * @return mixed|string
     */
    public function getMerchantId($storeId = null)
    {
        return $this->getConfigData(self::MERCHANT_ID, $storeId);
    }

    /**
     * @param $storeId
     * @return mixed|string
     */
    public function getPaymentConcept($storeId = null)
    {
        return $this->getConfigData(self::CONCEPT, $storeId);
    }
    /**
     * @param $storeId
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getCompanyName($storeId = null)
    {
        return $this->storeManager->getStore($storeId)->getName();
    }

    /**
     * @param $storeId
     * @return bool
     */
    public function getSandbox($storeId = null)
    {
        return (bool)$this->getConfigData(self::SANDBOX, $storeId);
    }


    /**
     * @param $storeId
     * @return mixed|string
     */
    public function getCancelHours($storeId = null)
    {
        return $this->getConfigData(self::CANCEL_HOURS, $storeId) ?? '';
    }

    /**
     * @param $storeId
     * @return bool
     */
    public function getWidgetsEnabled($storeId = null)
    {
        return (bool)$this->getConfigData(self::WIDGETS_ENABLED, $storeId);
    }

    /**
     * PDP widget mode: disabled (0), default Powerpay widget (1) or neutral widget (2).
     *
     * @param $storeId
     * @return int
     */
    public function getProductWidgetMode($storeId = null)
    {
        return (int)$this->getConfigData(self::PRODUCT_WIDGET, $storeId);
    }

    /**
     * @param $storeId
     * @return bool
     */
    public function getHeaderWidgetEnabled($storeId = null)
    {
        return (bool)$this->getConfigData(self::HEADER_WIDGET, $storeId);
    }

    /**
     * @param $storeId
     * @return bool
     */
    public function getBannerWidgetEnabled($storeId = null)
    {
        return (bool)$this->getConfigData(self::BANNER_WIDGET, $storeId);
    }


    /**
     * @param $storeId
     * @return bool
     */
    public function getCheckoutWidgetEnabled($storeId = null)
    {
        return (bool)$this->getConfigData(self::CHECKOUT_WIDGET, $storeId);
    }

    /**
     * @param $storeId
     * @return bool
     */
    public function getCustomSuccess($storeId = null)
    {
        return (bool)$this->getConfigData(self::CUSTOM_SUCCESS, $storeId);
    }

    /**
     * Errors are always written to error.log; every level goes to debug.log when Debug is enabled.
     *
     * @param string $message
     * @param string $type critical|error|warning|info|debug
     * @return void
     */
    public function log($message, $type = 'debug')
    {
        $level = self::LOG_LEVELS[$type] ?? LogLevel::INFO;

        if ($level === LogLevel::CRITICAL || $level === LogLevel::ERROR) {
            $this->errorLogger->log($level, $message);
        }
        if ($this->isDebugEnabled()) {
            $this->debugLogger->log($level, $message);
        }
    }

    /**
     * @param Order $order
     * @return string
     */
    public function generateToken($order)
    {
        return hash('sha256', $this->getSecret($order->getStoreId()) .  $order->getIncrementId() . $order->getCreatedAt());
    }
}
