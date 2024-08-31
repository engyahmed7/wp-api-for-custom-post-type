<?php

class Cpta_Custom_Post_Type_API_Auth
{
    /**
     * Summary of init
     * Initializes the authentication class.
     * @return void
     */
    public function init()
    {
        add_action('rest_api_init', array($this, 'check_jwt_auth'));
    }

    /**
     * Summary of check_jwt_auth
     * Adds a filter to check the namespace.
     * @return void
     */
    public function check_jwt_auth()
    {
        add_filter('rest_pre_dispatch', array($this, 'check_namespace_and_add_jwt_auth'), 10, 3);
    }

    /**
     * Summary of check_namespace_and_add_jwt_auth
     * Checks the namespace and excludes specific routes.
     * @param mixed $result 
     * @param WP_REST_Server $server 
     * @param WP_REST_Request $request 
     * @return mixed
     */
    public function check_namespace_and_add_jwt_auth($result, $server, $request)
    {
        if (defined('JWT_AUTH_SECRET_KEY')) {
            $namespace = '/api/v1/';
            $jwt_auth_route = '/jwt-auth/v1/token';

            $current_route = $request->get_route();

            if (strpos($current_route, $namespace) !== false && strpos($current_route, $jwt_auth_route) === false) {

                return $result;
            }
        }

        return $result;
    }
}