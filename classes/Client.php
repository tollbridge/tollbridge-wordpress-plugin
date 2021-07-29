<?php

namespace Tollbridge\Paywall;

use Tollbridge\Paywall\Exceptions\MissingConnectionSettingsException;
use Tollbridge\Paywall\Exceptions\NoPlansExistException;
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

        $accessToken = wp_cache_get('tollbridgeAccessToken', 'tollbridge');

        if (!empty($accessToken)) {
            return $accessToken;
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

        if (is_wp_error($response)) {
            throw new ResponseErrorReceivedException("Error code received from remote server: ".$response->get_error_message());
        }

        if ($response['response']['code'] != 200) {
            throw new ResponseErrorReceivedException("Error code ".$response['response']['code']." received from remote server.");
        }

        $data = json_decode($response['body']);

        wp_cache_set('tollbridgeAccessToken', $data->access_token, 'tollbridge', 900);

        return $data->access_token;
    }


    /**
     * @throws \Tollbridge\Paywall\Exceptions\ResponseErrorReceivedException
     */
    public function getPlans()
    {
        $plans = wp_cache_get('tollbridgePlans', 'tollbridge');

        if (!empty($plans)) {
            return $plans;
        }

        if (!$this->canAttemptConnection()) {
            throw new MissingConnectionSettingsException();
        }

        $response = wp_remote_get('https://'.$this->appId.'/api/config');

        if ($response['response']['code'] != 200) {
            throw new ResponseErrorReceivedException("The Tollbridge server has returned an error (".$response['response']['code']."). Please try again later.");
        }

        $data = json_decode($response['body'], true);

        if (empty($data['plans'])) {
            throw new NoPlansExistException();
        }

        $plans = [];

        foreach ($data['plans'] as $plan) {
            $plans[$plan['id']] = $plan['name'];
        }

        wp_cache_set('tollbridgePlans', $plans, 'tollbridge', 900);

        return $plans;
    }

    /**
     * @throws \Tollbridge\Paywall\Exceptions\ResponseErrorReceivedException
     */
    public function getViews()
    {
        $views = wp_cache_get('tollbridgeViews', 'tollbridge');

        if (!empty($views)) {
            return $views;
        }

        if (!$this->canAttemptConnection()) {
            throw new MissingConnectionSettingsException();
        }

        $response = wp_remote_get('https://'.$this->appId.'/api/amp/views');

        if ($response['response']['code'] != 200) {
            throw new ResponseErrorReceivedException("The Tollbridge server has returned an error (".$response['response']['code']."). Please try again later.");
        }

        $data = json_decode($response['body'], true);

        if (empty($data)) {
            return [];
        }

        wp_cache_set('tollbridgeViews', $data, 'tollbridge', 900);

        return $data;
    }
}
