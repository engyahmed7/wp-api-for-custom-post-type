<?php

/**
 * Plugin Name: Custom Post Type API
 * Description: A plugin to manage custom post types via a REST API with JWT authentication.
 * Version: 1.0.0
 * Author: engy
 * Text Domain: custom-post-type-api
 */

if (! defined('ABSPATH')) {
    exit;
}

define('CPT_API_PLUGIN_DIR_PATH', plugin_dir_path(__FILE__));

require_once CPT_API_PLUGIN_DIR_PATH . 'includes/classes/class-custom-post-type-api.php';
require_once CPT_API_PLUGIN_DIR_PATH . 'includes/classes/class-custom-post-type-api-rest.php';
require_once CPT_API_PLUGIN_DIR_PATH . 'includes/classes/class-custom-post-type-api-auth.php';

function cpta_api_init()
{
    $plugin = new Cpta_Custom_Post_Type_Api_Register();
    $plugin->run();
}

add_action('plugins_loaded', 'cpta_api_init');
