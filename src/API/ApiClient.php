<?php
namespace CommerIQ\API;

defined('ABSPATH') || exit;

class ApiClient
{
    protected $endpoint;
    protected $apiKey;
    protected $apiSecret;

    public function __construct($endpoint = '', $apiKey = '', $apiSecret = '')
    {
        $this->endpoint = $endpoint;
        $this->apiKey = $apiKey;
        $this->apiSecret = $apiSecret;
    }

    public function setCredentials($endpoint, $apiKey, $apiSecret)
    {
        $this->endpoint = $endpoint;
        $this->apiKey = $apiKey;
        $this->apiSecret = $apiSecret;
    }

    public function signPayload($body, $timestamp)
    {
        return hash_hmac('sha256', $timestamp . '.' . $body, $this->apiSecret);
    }

    public function postComparison(array $payload)
    {
        $body = wp_json_encode($payload);
        $ts = time();
        $signature = $this->signPayload($body, $ts);

        $headers = [
            'Content-Type' => 'application/json',
            'X-COMMERIQ-APIKEY' => $this->apiKey,
            'X-COMMERIQ-TIMESTAMP' => $ts,
            'X-COMMERIQ-SIGNATURE' => $signature,
        ];

        $response = wp_remote_post($this->endpoint, [
            'headers' => $headers,
            'body' => $body,
            'timeout' => 20,
        ]);

        if (is_wp_error($response)) {
            throw new \Exception(esc_html($response->get_error_message()));
        }

        $code = wp_remote_retrieve_response_code($response);
        $body = wp_remote_retrieve_body($response);

        if ($code !== 200) {
            throw new \Exception(esc_html('API returned HTTP ' . $code . ': ' . $body));
        }

        $data = json_decode($body, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \Exception('Invalid JSON response from API');
        }

        return $data;
    }
}
