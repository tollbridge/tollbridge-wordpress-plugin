<?php

namespace Tollbridge\Paywall;

use Tollbridge\Paywall\Exceptions\MissingConnectionSettingsException;
use Tollbridge\Paywall\Exceptions\NoPlansExistException;
use Tollbridge\Paywall\Exceptions\ResponseErrorReceivedException;
use WP_Error;

/**
 * Handle remote interface with Tollbridge API.
 */
class Client {

    private static $instance = null;

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

    public static function getInstance(): ?Client {
        if ( self::$instance == null ) {
            self::$instance = new Client();
        }

        return self::$instance;
    }

    public function canAttemptConnection() {
        $manager = new Manager();

        if ( !$manager->allAccountSettingsAreEntered() ) {
            return false;
        }

        $this->appId        = $manager->getAppId();
        $this->clientId     = $manager->getClientId();
        $this->clientSecret = $manager->getClientSecret();

        return true;
    }

    /**
     * @throws \Tollbridge\Paywall\Exceptions\MissingConnectionSettingsException
     * @throws \Tollbridge\Paywall\Exceptions\ResponseErrorReceivedException
     */
    public function getAccessToken() {
        if ( !$this->canAttemptConnection() ) {
            throw new MissingConnectionSettingsException();
        }

        $accessToken = wp_cache_get( 'tollbridgeAccessToken', 'tollbridge' );

        if ( !empty( $accessToken ) ) {
            return $accessToken;
        }

        $data = [
            'grant_type'    => 'client_credentials',
            'client_id'     => $this->clientId,
            'client_secret' => $this->clientSecret,
            'scope'         => '',
        ];

        $url      = 'https://' . $this->appId . '/oauth/token';
        $response = wp_remote_post( $url, [
            'body' => $data,
        ] );

        if ( is_wp_error( $response ) ) {
            throw new ResponseErrorReceivedException( __( 'Error code received from remote server: ', 'tollbridge' ) . $response->get_error_message() );
        }

        if ( $response['response']['code'] != 200 ) {
            throw new ResponseErrorReceivedException( __( 'Error code ', 'tollbridge' ) . $response['response']['code'] . __( ' received from remote server.', 'tollbridge' ) );
        }

        $data = json_decode( $response['body'] );

        wp_cache_set( 'tollbridgeAccessToken', $data->access_token, 'tollbridge', 900 );

        return $data->access_token;
    }

    /**
     * @throws \Tollbridge\Paywall\Exceptions\ResponseErrorReceivedException
     * @throws \Tollbridge\Paywall\Exceptions\MissingConnectionSettingsException
     */
    public function getConfig() {
        if ( !empty( $this->config ) ) {
            return $this->config;
        }

        if ( !$this->canAttemptConnection() ) {
            throw new MissingConnectionSettingsException();
        }

        global $wp;
        $response = wp_remote_get( 'https://' . $this->appId . '/api/config', [
            'url' => add_query_arg( $wp->query_vars, home_url() ),
        ] );

        if ( is_wp_error( $response ) ) {
            throw new ResponseErrorReceivedException( __( 'Error code received from remote server: ', 'tollbridge' ) . $response->get_error_message() );
        }

        if ( $response['response']['code'] != 200 ) {
            throw new ResponseErrorReceivedException( __( 'The Tollbridge server has returned an error', 'tollbridge' ) . ' (' . $response['response']['code'] . '). ' . __( 'Please try again later.', 'tollbridge' ) );
        }

        return $this->config = json_decode( $response['body'], true );
    }

    /**
     * @throws \Tollbridge\Paywall\Exceptions\ResponseErrorReceivedException
     * @throws \Tollbridge\Paywall\Exceptions\MissingConnectionSettingsException
     * @throws \Tollbridge\Paywall\Exceptions\NoPlansExistException
     */
    public function getPlans() {
        $plans = wp_cache_get( 'tollbridgePlans', 'tollbridge' );

        if ( !empty( $plans ) ) {
            return $plans;
        }

        $data = $this->getConfig();

        if ( empty( $data['plans'] ) ) {
            throw new NoPlansExistException();
        }

        $plans = [];

        foreach ( $data['plans'] as $plan ) {
            $plans[$plan['id']] = $plan['name'];
        }

        wp_cache_set( 'tollbridgePlans', $plans, 'tollbridge', 900 );

        return $plans;
    }

    /**
     * @throws \Tollbridge\Paywall\Exceptions\ResponseErrorReceivedException
     * @throws \Tollbridge\Paywall\Exceptions\MissingConnectionSettingsException
     */
    public function getViews() {
        $views = wp_cache_get( 'tollbridgeViews', 'tollbridge' );

        if ( !empty( $views ) ) {
            return $views;
        }

        if ( !$this->canAttemptConnection() ) {
            throw new MissingConnectionSettingsException();
        }

        $response = wp_remote_get( 'https://' . $this->appId . '/api/amp/views' );

        if ( $response['response']['code'] != 200 ) {
            throw new ResponseErrorReceivedException( __( 'The Tollbridge server has returned an error', 'tollbridge' ) . ' (' . $response['response']['code'] . '). ' . __( 'Please try again later.', 'tollbridge' ) );
        }

        $data = json_decode( $response['body'], true );

        if ( empty( $data ) ) {
            return [];
        }

        wp_cache_set( 'tollbridgeViews', $data, 'tollbridge', 900 );

        return $data;
    }
}
