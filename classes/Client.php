<?php

namespace Tollbridge\Paywall;

use Tollbridge\Paywall\Exceptions\MissingConnectionSettingsException;
use Tollbridge\Paywall\Exceptions\ResponseErrorReceivedException;

/**
 * Handle remote interface with Tollbridge API.
 */
class Client
{
    /**
     * @var string
     */
    private $appId;

    /**
     * @var string
     */
    private $clientId;

    /**
     * @var string
     */
    private $clientSecret;

    public function canAttemptConnection()
    {
        $manager = new Manager();
        if (!$manager->allAccountSettingsAreEntered()) {
            return false;
        }

        $this->appId = $manager->getAppId();
        $this->clientId = $manager->getClientId();
        $this->clientSecret = $manager->getClientSecret();

        return true;
    }


    /**
     * @throws \Tollbridge\Paywall\Exceptions\MissingConnectionSettingsException
     * @throws \Tollbridge\Paywall\Exceptions\ResponseErrorReceivedException
     */
    public function getAccessToken()
    {
        if (!$this->canAttemptConnection()) {
            throw new MissingConnectionSettingsException();
        }

        $data = [
            'grant_type' => 'client_credentials',
            'client_id' => $this->clientId,
            'client_secret' => $this->clientSecret,
            'scope' => ''
        ];

        $url = 'https://'.$this->appId.'/oauth/token';
        $response = wp_remote_post($url, [
            'body' => $data
        ]);

        if ($response['response']['code'] != 200) {
            throw new ResponseErrorReceivedException("Error code ".$response['response']['code']." received from remote server.");
        }

        $data = json_decode($response['body']);

        return $data->access_token;
    }


    /**
     * @throws \Tollbridge\Paywall\Exceptions\ResponseErrorReceivedException
     */
    public function getPlans()
    {
        $token = $this->getAccessToken();

        $response = wp_remote_get('https://'.$this->appId.'/api/config/plans', [
            'headers' => [
                'Authorization' => 'Bearer '.$token,
            ]
        ]);

        if ($response['response']['code'] != 200) {
            throw new ResponseErrorReceivedException("Error code ".$response['response']['code']." received from remote server.");
        }

        $data = json_decode($response['body'], true);
        return array_column($data['data'], 'name');
    }
}
