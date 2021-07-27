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


    /**
     * @var array
     */
    private $plansCache;

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

        if (is_wp_error($response)) {
            throw new ResponseErrorReceivedException("Error code received from remote server: ".$response->get_error_message());
        }

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
        if (!empty($this->plansCache)) {
            return $this->plansCache;
        }

        $token = $this->getAccessToken();

        $response = wp_remote_get('https://'.$this->appId.'/api/config', [
            'headers' => [
                'Authorization' => 'Bearer '.$token,
            ]
        ]);

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

        $this->plansCache = $plans;

        return $this->plansCache;
    }

    /**
     * @throws \Tollbridge\Paywall\Exceptions\ResponseErrorReceivedException
     */
    public function getViews()
    {
        if (!empty($this->viewsCache)) {
            return $this->viewsCache;
        }

        $token = $this->getAccessToken();

        $response = wp_remote_get('https://'.$this->appId.'/api/amp/views', [
            'headers' => [
                'Authorization' => 'Bearer '.$token,
            ]
        ]);

        if ($response['response']['code'] != 200) {
            throw new ResponseErrorReceivedException("The Tollbridge server has returned an error (".$response['response']['code']."). Please try again later.");
        }

        $data = json_decode($response['body'], true);

        if (empty($data)) {
            return [];
        }

        $this->viewsCache = $data;

        return $this->viewsCache;
    }
}
