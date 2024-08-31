<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class Custom_Post_Type_API_Frontend
{
    public $token_option_key = 'custom_api_jwt_token';
    public $refresh_token_option_key = 'custom_api_jwt_refresh_token';
    public $token_url = 'http://localhost/wordpress/wp-json/jwt-auth/v1/token';
    public $token_validate_url = 'http://localhost/wordpress/wp-json/jwt-auth/v1/token/validate';

    /**
     * Authenticate and get a JWT token.
     *
     * @param string $username
     * @param string $password
     * @return array|WP_Error
     */
    function authenticate($username, $password, $deivce = 'web')
    {
        $response = wp_remote_post($this->token_url, [
            'method'    => 'POST',
            'body'      => [
                'username' => $username,
                'password' => $password,
                'device' => $deivce
            ],
            'headers'   => [
                'Content-Type' => 'application/x-www-form-urlencoded'
            ]
        ]);

        if (is_wp_error($response)) {
            return new WP_Error('authentication_failed', __('Authentication failed.' . $response->get_error_message(), 'custom-post-type-api'));
        }

        $response_body = wp_remote_retrieve_body($response);
        $data = json_decode($response_body, true);
        echo '<pre>' . print_r($data, true) . '</pre>';
        echo 'Token' . $data['data']['token'];

        if (isset($data['data']['token'])) {

            $refresh_token = $response['cookies'][0]->value;
            echo 'refresh_token' . $refresh_token;

            update_option($this->token_option_key, $data['data']['token']);
            update_option($this->refresh_token_option_key, $refresh_token);
            return $data['token'];
        }

        return new WP_Error('authentication_error', __('Authentication error.', 'custom-post-type-api'));
    }

    /**
     * Validate the JWT token.
     *
     * @return bool|WP_Error
     */
    public function validate_token()
    {
        $token = get_option($this->token_option_key);
        if (!$token) {
            return new WP_Error('no_token', __('No token found.', 'custom-post-type-api'));
        }

        $response = wp_remote_post($this->token_validate_url, [
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
            ],
        ]);
        // echo '<pre>' . print_r($response, true) . '</pre>';
        if (is_wp_error($response)) {
            error_log('Token validation error: ' . $response->get_error_message());
            return new WP_Error('validation_failed', __('error: ' . $response->get_error_message(), 'custom-post-type-api'));
        }
        
        $response_body = wp_remote_retrieve_body($response);
        $data = json_decode($response_body, true);
        echo '<pre>' . print_r($data, true) . '</pre>';

        if (isset($data['code']) && $data['code'] === 'jwt_auth_valid_token' && $data['statusCode'] === 200) {
            return true;
        }

        return new WP_Error('invalid_token', __('Invalid token.', 'custom-post-type-api'));
    }

    /**
     * Refresh the JWT token.
     *
     * @return string|WP_Error
     */
    public function refresh_token()
    {
        $refresh_token = get_option($this->refresh_token_option_key);

        if (!$refresh_token) {
            return new WP_Error('no_refresh_token', __('No refresh token found.', 'custom-post-type-api'));
        }

        $response = wp_remote_post($this->token_url, [
            'method'    => 'POST',
            'body'      => [
                'refresh_token' => $refresh_token
            ],
            'headers'   => [
                'Content-Type' => 'application/x-www-form-urlencoded'
            ]
        ]);

        if (is_wp_error($response)) {
            return new WP_Error('refresh_failed', __('Token refresh failed.', 'custom-post-type-api'));
        }

        $response_body = wp_remote_retrieve_body($response);
        $data = json_decode($response_body, true);

        if (isset($data['token'])) {
            update_option($this->token_option_key, $data['token']);
            if (isset($data['refresh_token'])) {
                update_option($this->refresh_token_option_key, $data['refresh_token']);
            }
            return $data['token'];
        }

        return new WP_Error('refresh_error', __('Token refresh error.', 'custom-post-type-api'));
    }

    /**
     * Fetch data from an external site with token management.
     *
     * @param string $endpoint
     * @param string $method
     * @param string $body
     * @return array|WP_Error
     */
    function fetch_data_from_external_site($endpoint, $method = 'GET', $body = '')
    {
        $validation = $this->validate_token();

        echo '<pre>';
        print_r($validation);
        echo '</pre>';
        if (is_wp_error($validation)) {
            $refresh = $this->refresh_token();

            if (is_wp_error($refresh)) {
                return $refresh;
            }
        }

        $token = get_option($this->token_option_key);

        $headers = [
            'Content-Type' => 'application/json',
        ];

        if ($token) {
            $headers['Authorization'] = 'Bearer ' . $token;
        }

        $response = wp_remote_request($endpoint, [
            'method'    => $method,
            'headers'   => $headers,
            'body'      => $body,
        ]);

        if (is_wp_error($response)) {
            return new WP_Error('api_request_failed', __($response->get_error_message(), 'custom-post-type-api'));
        }

        $response_body = wp_remote_retrieve_body($response);
        return json_decode($response_body, true);
    }
}