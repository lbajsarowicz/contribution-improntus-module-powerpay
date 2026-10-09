<?php

namespace Improntus\PowerPay\Model\Rest;

use Magento\Framework\HTTP\Client\Curl;
use Improntus\PowerPay\Helper\Data;

class WebService
{

    /**
     * @var Data
     */
    private $helper;

    /**
     * @var Curl
     */
    private $curl;

    public function __construct(
        Curl $curl,
        Data $helper
    )
    {
        $this->helper = $helper;
        $this->curl = $curl;
    }

    public function doRequest($endpoint, $secret,  $data=null, $method=null, $storeId = null, $options=null)
    {
        if (null === $method) {
            $method = "POST";
        }

        $url = $this->helper->getApiBaseUrl($storeId) . $endpoint;

        $basic = base64_encode($secret);

        try
        {
            if (!is_null($options)) {
                $this->curl->setOptions($options);
            }
            $this->curl->setHeaders(
                [
                    "Authorization" => $basic,
                    "Content-Type" => "application/json"
                ]
            );
            $data = json_encode($data);
            $this->helper->log(sprintf(
                "REQUEST %s %s\nAUTHORIZATION: %s\nPAYLOAD: %s",
                $method,
                $url,
                $this->formatAuthorization($basic, $storeId),
                $data
            ));
            switch ($method)
            {
                case "POST":
                    $this->curl->post($url, $data);
                    break;
                case "GET":
                    $this->curl->get($url);
            }

            $status = $this->curl->getStatus();
            $body = $this->curl->getBody();
            $result = json_decode($body, true);
            $this->helper->log(
                sprintf("RESPONSE %s %s\nSTATUS: %s\nBODY: %s", $method, $url, $status, $body),
                ($status >= 200 && $status < 300 && !isset($result['errors'])) ? 'info' : 'error'
            );

            return $result;
        } catch (\Exception $e) {
            $this->helper->log("REQUEST {$method} {$url} failed: {$e->getMessage()}", 'error');
            return $e->getMessage();
        }
    }

    /**
     * @param string $authorization
     * @param int|string|null $storeId
     * @return string
     */
    private function formatAuthorization($authorization, $storeId = null)
    {
        if (!$this->helper->isAuthTokenMasked($storeId)) {
            return $authorization;
        }
        return sprintf('sha256=%s len=%d', hash('sha256', $authorization), strlen($authorization));
    }
}
