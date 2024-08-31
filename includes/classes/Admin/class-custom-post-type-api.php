<?php

class Cpta_Custom_Post_Type_Api_Register
{

    /**
     * Summary of __construct
     * 
     * Sets up the class by including necessary files and registering the custom post type on the `init` hook.
     */
    public function __construct()
    {
        $this->include_files();
        add_action('init', array($this, 'register_custom_post_type'));
    }


    /**
     * Summary of include_files
     * Includes necessary files for REST API and authentication functionality.
     * @return void
     */
    public function include_files()
    {
        require_once CPT_API_PLUGIN_DIR_PATH . 'includes/classes/Admin/class-custom-post-type-api-rest.php';
        require_once CPT_API_PLUGIN_DIR_PATH . 'includes/classes/Admin/class-custom-post-type-api-auth.php';
    }

    /**
     * Summary of run
     * Initializes the REST API and authentication functionalities.
     * @return void
     */
    public function run()
    {
        $api = new Cpta_Custom_Post_Type_API_REST();
        $api->init();

        $auth = new Cpta_Custom_Post_Type_API_Auth();
        $auth->init();
    }

    /**
     * Summary of register_custom_post_type
     * Registers a custom post type called 'custom_post'
     * @return void
     */
    function register_custom_post_type()
    {
        $labels = array(
            'name'               => __('Custom Posts', 'custom-post-type-api'),
            'singular_name'      => __('Custom Post', 'custom-post-type-api'),
            'menu_name'          => __('Custom Posts', 'custom-post-type-api'),
            'name_admin_bar'     => __('Custom Post', 'custom-post-type-api'),
            'add_new'            => __('Add New', 'custom-post-type-api'),
            'add_new_item'       => __('Add New Custom Post', 'custom-post-type-api'),
            'new_item'           => __('New Custom Post', 'custom-post-type-api'),
            'edit_item'          => __('Edit Custom Post', 'custom-post-type-api'),
            'view_item'          => __('View Custom Post', 'custom-post-type-api'),
            'all_items'          => __('All Custom Posts', 'custom-post-type-api'),
            'search_items'       => __('Search Custom Posts', 'custom-post-type-api'),
            'parent_item_colon'  => __('Parent Custom Posts:', 'custom-post-type-api'),
            'not_found'          => __('No custom posts found.', 'custom-post-type-api'),
            'not_found_in_trash' => __('No custom posts found in Trash.', 'custom-post-type-api'),
        );

        $args = array(
            'labels'             => $labels,
            'public'             => true,
            'publicly_queryable' => true,
            'show_ui'            => true,
            'show_in_menu'       => true,
            'query_var'          => true,
            'rewrite'            => array('slug' => 'custom_post_type'),
            'capability_type'    => 'post',
            'has_archive'        => true,
            'hierarchical'       => false,
            'menu_position'      => 5,
            'supports'           => array('title', 'editor', 'excerpt', 'thumbnail'),
        );

        register_post_type('custom_post', $args);
    }
}