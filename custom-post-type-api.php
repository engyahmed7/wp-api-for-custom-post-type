<?php

/**
 * Plugin Name: Custom Post Type API
 * Description: A plugin to manage custom post types via a REST API with JWT authentication.
 * Version: 1.0.0
 * Author: engy
 * Text Domain: custom-post-type-api
 */

if (!defined('ABSPATH')) {
    exit;
}

define('CPT_API_PLUGIN_DIR_PATH', plugin_dir_path(__FILE__));

require_once CPT_API_PLUGIN_DIR_PATH . 'includes/classes/Admin/class-custom-post-type-api.php';
require_once CPT_API_PLUGIN_DIR_PATH . 'includes/classes/Admin/class-custom-post-type-api-rest.php';
require_once CPT_API_PLUGIN_DIR_PATH . 'includes/classes/Admin/class-custom-post-type-api-auth.php';
require_once CPT_API_PLUGIN_DIR_PATH . 'includes/classes/Front/class-custom-post-type-api-frontend.php';

function cpta_api_init()
{
    $plugin = new Cpta_Custom_Post_Type_Api_Register();
    $plugin->run();
    delete_transient('custom_api_jwt_token');
    delete_transient('custom_api_jwt_refresh_token');
}

add_action('plugins_loaded', 'cpta_api_init');

function cpta_api_menu()
{
    add_menu_page(
        'Custom API Settings',
        'API Settings',
        'manage_options',
        'cpta-api-settings',
        'cpta_api_settings_page'
    );
}
add_action('admin_menu', 'cpta_api_menu');

function cpta_api_settings_page()
{
    $api = new Custom_Post_Type_API_Frontend();
    $token = get_option($api->token_option_key);
    $validation = $api->authenticate('engy', 'EngyAhmed22001');
    $refresh_token = get_option($api->refresh_token_option_key);

    if (is_wp_error($validation)) {
        echo '<div class="notice notice-error"><p>' . $validation->get_error_message() . '</p></div>';
    } else {
        echo '<div class="notice notice-success"><p>Token has been successfully generated.</p></div>';
        echo '<div class="notice notice-success"><p><strong>Token:</strong> ' . esc_html($token) . '</p></div>';
        echo '<div class="notice notice-success"><p><strong>refresh_token:</strong> ' . esc_html($refresh_token) . '</p></div>';
    }

    $post = [
        'title' => 'New Post from API custom posttt',
        'content' => 'This is a new post.',

    ];

    // $response = $api->fetch_data_from_external_site('http://localhost/wordpress/wp-json/api/v1/posts', 'POST', json_encode($post));
    // if (is_wp_error($response)) {
    //     echo '<div class="notice notice-error"><p>' . $response->get_error_message() . '</p></div>';
    // } else {
    //     echo '<div class="notice notice-success"><p>New post has been successfully added.</p></div>';
    //     echo '<pre>';
    //     print_r($response);
    //     echo '</pre>';
    // }

    $getResponse = $api->fetch_data_from_external_site('http://localhost/wordpress/wp-json/api/v1/posts');
    if (is_wp_error($getResponse)) {
        echo '<div class="notice notice-error"><p>' . $getResponse->get_error_message() . '</p></div>';
    } else {
        echo '<div class="notice notice-success"><p>Posts have been successfully fetched.</p></div>';
        echo '<pre>';
        print_r($getResponse);
        echo '</pre>';
    }

?>
<div class="wrap">

    <h1>API Settings</h1>

</div>
<?php
}