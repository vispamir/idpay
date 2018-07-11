<?php

/**
 * @file
 * Rest service handler by GuzzleHttp API.
 * 
 * PHP version ^5.6
 * 
 * @author  Amir Koulivand <amir@koulivand.ir>
 * @copyright   2016-2018 The IDPay group
 */

namespace Idpay\Bin;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

/**
 * Request handler.
 */
class RestService
{
    protected $apiKey = 'xxxx-xxxx-xxxx-xxxx';
    protected $endpoint = 'https://www.idpay.ir/api/service/v1/';

    /**
     * Create payment request.
     *
     * @param $data
     *
     * @return array
     */
    public function paymentRequest($data)
    {
        $result = $this->httpRequest('payment', $data);

        if ($result['Status'] == 201) {
            return [
                'Status' => 'success',
                'Result' => $result,
            ];
        } else {
            return [
                'Status' => 'error',
                'Message' => $result,
            ];
        }
    }

    /**
     * Inquiry request.
     *
     * @param $inputs
     *
     * @return array
     */
    public function inquiryRequest($data)
    {
        $result = $this->httpRequest('payment/inquiry', $data);

        if ($result['Status'] == 200) {
            return [
                'Status' => 'success',
                'Result' => $result,
            ];
        } else {
            return [
                'Status' => 'error',
                'error'  => !empty($result['message']) ? $result['message'] : null,
            ];
        }
    }

    /**
     * Send request by Client api pass response.
     *
     * @param $resource
     * @param $data
     *
     * @return mixed
     */
    private function httpRequest($resource, $data)
    {
        try {
            $client = new Client(['base_uri' => $this->endpoint]);
            $response = $client->request(
                'POST', $resource, [
                    'json' => $data,
                    'headers' => [
                        'Accept'        => 'application/json',
                        'User-Agent'    => 'idpay/1.0',
                        'IDPay-API-Key' => $this->apiKey
                    ]
                ]
            );

            $status = $response->getStatusCode();
            $result = $response->getBody()->getContents();
            $result = json_decode($result, true);
        } catch (RequestException $e) {
            $result = [
                'Status' => false,
                'message' => "Connection failed.",
            ];
            $response = $e->getResponse();
            if (!is_null($response)) {
                $result = $response->getBody()->getContents();
                $result = json_decode($result, true);
            }
        }

        return $result;
    }

    /**
     * @param mixed $endpoint
     *
     * @return void
     */
    public function setEndpoint($endpoint)
    {
        $this->endpoint = $endpoint;
    }

    /**
     * @param mixed $apiKey
     *
     * @return void
     */
    public function setApiKey($apiKey)
    {
        $this->apiKey = $apiKey;
    }
}