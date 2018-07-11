<?php

/**
 * @file
 * Methods of IDPay services.
 * 
 * PHP version ^5.6
 * 
 * @author  Amir Koulivand <amir@koulivand.ir>
 * @copyright   2016-2018 The IDPay group
 */

namespace Idpay;

use Idpay\Bin\RestService;

/**
 * IDPay payment and inquiry service.
 */
class Idpay
{
    private $service;
    private $paymentPath = 'https://www.idpay.ir/p/ws/';

    public function __construct($apiKey, $endpoint)
    {
        $this->service = new RestService();
        $this->service->setEndpoint($endpoint);
        $this->service->setApiKey($apiKey);
    }

    /**
     * Create new payment on Idpay
     * Get payment path and payment id.
     *
     * @param string $callback
     * @param string $amount
     * @param string $name
     * @param string $phone
     * @param string $description
     *
     * @return array|@paymentPath
     */
    public function payment($callback, $orderId, $amount, $name = null, $phone = null, $description = null)
    {
        $data = [
            'amount'        => $amount,
            'order_id'      => $orderId,
            'callback'      => $callback,
        ];

        if (!is_null($name) && !empty($name)) {
            $data['name'] = $name;
        }
        if (!is_null($phone) && !empty($phone)) {
            $data['phone'] = $phone;
        }
        if (!is_null($description) && !empty($description)) {
            $data['description'] = $description;
        }

        $result = $this->service->paymentRequest($data);

        if ($result['Status'] == 'success') {
            if (isset($result['Result'])) {
                $this->paymentPath = $result['Result']['link'];
            }

            return $result['Result'];
        }

        return false;
    }

    /**
     * Inquiry payment result by trackId and orderId.
     *
     * @param $trackId
     * @param $orderId
     *
     * @return array
     */
    public function inquiry($trackId, $orderId, $amount)
    {
        $data = [
            'track_id' => $trackId,
            'order_id'  => $orderId
        ];

        $result = $this->service->inquiryRequest($data);
        $result['success'] = !empty($result['amount']) && $result['amount'] == $amount;

        return $result;
    }

    /**
     * Redirect user to the received payment path.
     */
    public function gotoPaymentPath()
    {
        header('Location: '. $this->paymentPath);
        exit;
    }
}