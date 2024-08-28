    <?php

    class Cpta_Custom_Post_Type_API_REST extends WP_REST_Controller
    {

        /**
         * Summary of __construct
         * Initializes the REST API class with default namespace and base.
         */
        public function __construct()
        {
            $this->namespace = 'api/v1';
            $this->rest_base = 'posts';
        }

        /**
         * Summary of init
         * Registers REST API routes on the `rest_api_init` hook.
         * @return void
         */
        public function init()
        {
            add_action('rest_api_init', array($this, 'register_routes'));
        }

        /**
         * Summary of register_routes
         * Registers REST API routes for the custom post type.
         * @return void
         */
        public function register_routes()
        {
            register_rest_route($this->namespace, '/' . $this->rest_base, array(
                array(
                    'methods'             => WP_REST_Server::READABLE,
                    'callback'            => array($this, 'get_items'),
                    'permission_callback' => array($this, 'get_items_permissions_check'),
                ),
                array(
                    'methods'             => WP_REST_Server::CREATABLE,
                    'callback'            => array($this, 'create_item'),
                    'permission_callback' => array($this, 'create_item_permissions_check'),
                    'args' => $this->get_endpoint_args_for_item_schema(true),
                ),
            ));

            register_rest_route($this->namespace, '/' . $this->rest_base . '/(?P<id>\d+)', array(
                array(
                    'methods'             => WP_REST_Server::READABLE,
                    'callback'            => array($this, 'get_item'),
                    'permission_callback' => array($this, 'get_item_permissions_check'),
                ),
                array(
                    'methods'             => WP_REST_Server::EDITABLE,
                    'callback'            => array($this, 'update_item'),
                    'permission_callback' => array($this, 'update_item_permissions_check'),
                ),
                array(
                    'methods'             => WP_REST_Server::DELETABLE,
                    'callback'            => array($this, 'delete_item'),
                    'permission_callback' => array($this, 'delete_item_permissions_check'),
                ),
            ));
        }

        /**
         * Checks if the current user has the 'administrator' role for accessing the items.
         *
         * @param WP_REST_Request $request The current request object.
         * @return bool|WP_Error True if user has 'administrator' role, WP_Error otherwise.
         */
        public function get_items_permissions_check($request)
        {
            if (!current_user_can('manage_options')) {
                return new WP_Error(
                    'rest_forbidden',
                    __('You are not allowed to access this resource.', 'custom-post-type-api'),
                    array('status' => rest_authorization_required_code())
                );
            }

            return true;
        }

        /**
         * Checks if the current user has the 'administrator' role for creating items.
         *
         * @param WP_REST_Request $request The current request object.
         * @return bool|WP_Error True if user has 'administrator' role, WP_Error otherwise.
         */
        public function create_item_permissions_check($request)
        {
            if (!current_user_can('manage_options')) {
                return new WP_Error(
                    'rest_forbidden',
                    __('You are not allowed to access this resource.', 'custom-post-type-api'),
                    array('status' => rest_authorization_required_code())
                );
            }

            return true;
        }

        /**
         * Checks if the current user has the 'administrator' role for accessing an individual item.
         *
         * @param WP_REST_Request $request The current request object.
         * @return bool|WP_Error True if user has 'administrator' role, WP_Error otherwise.
         */
        public function get_item_permissions_check($request)
        {
            if (!current_user_can('manage_options')) {
                return new WP_Error(
                    'rest_forbidden',
                    __('You are not allowed to access this resource.', 'custom-post-type-api'),
                    array('status' => rest_authorization_required_code())
                );
            }

            return true;
        }

        /**
         * Checks if the current user has the 'administrator' role for updating items.
         *
         * @param WP_REST_Request $request The current request object.
         * @return bool|WP_Error True if user has 'administrator' role, WP_Error otherwise.
         */
        public function update_item_permissions_check($request)
        {
            if (!current_user_can('manage_options')) {
                return new WP_Error(
                    'rest_forbidden',
                    __('You are not allowed to access this resource.', 'custom-post-type-api'),
                    array('status' => rest_authorization_required_code())
                );
            }

            return true;
        }

        /**
         * Checks if the current user has the 'administrator' role for deleting items.
         *
         * @param WP_REST_Request $request The current request object.
         * @return bool|WP_Error True if user has 'administrator' role, WP_Error otherwise.
         */
        public function delete_item_permissions_check($request)
        {
            if (!current_user_can('manage_options')) {
                return new WP_Error(
                    'rest_forbidden',
                    __('You are not allowed to access this resource.', 'custom-post-type-api'),
                    array('status' => rest_authorization_required_code())
                );
            }

            return true;
        }

        /**
         * Summary of get_posts
         * Get all posts
         * @return WP_REST_Response|WP_Error
         */
        public function get_items($request)
        {
            try {
                $args = array(
                    'post_type' => 'custom_post',
                    'numberposts' => -1
                );

                $posts = get_posts($args);
                if (empty($posts)) {
                    return new WP_REST_Response(array('message' => 'No posts found'), 404);
                }
                return new WP_REST_Response($posts, 200);
            } catch (Exception $e) {
                return new WP_Error('exception', $e->getMessage(), array('status' => 500));
            }
        }

        /**
         * Summary of create_post
         * Create a new post
         * @param mixed $data
         * @return WP_Error|WP_REST_Response
         */
        public function create_item($request)
        {
            try {
                $post_data = array(
                    'post_title'   => sanitize_text_field($request['title']),
                    'post_content' => sanitize_textarea_field($request['content']),
                    'post_type'    => 'custom_post',
                    'post_status'  => 'publish',
                );

                $post_id = wp_insert_post($post_data);

                if (is_wp_error($post_id)) {
                    return new WP_Error('create_failed', 'Failed to create post', array('status' => 500));
                }

                $post = get_post($post_id);
                return new WP_REST_Response($post, 201);
            } catch (Exception $e) {
                return new WP_Error('exception', $e->getMessage(), array('status' => 500));
            }
        }

        /**
         * Summary of update_post
         * Update a post
         * @param mixed $data
         * @return WP_Error|WP_REST_Response
         */
        public function update_item($request)
        {
            try {

                $post_id = $request->get_param('id');
                $post_data = array(
                    'ID'           => $post_id,
                    'post_title'   => sanitize_text_field($request['title']),
                    'post_content' => sanitize_textarea_field($request['content']),
                );

                $updated = wp_update_post($post_data, true);

                if (is_wp_error($updated)) {
                    return new WP_Error('update_failed', $updated->get_error_message(), array('status' => 500));
                }

                $post = get_post($post_id);
                return new WP_REST_Response($post, 200);
            } catch (Exception $e) {
                return new WP_Error('exception', $e->getMessage(), array('status' => 500));
            }
        }

        /**
         * Summary of delete_post
         * Delete a post
         * @param mixed $data
         * @return WP_Error|WP_REST_Response
         */
        public function delete_item($request)
        {
            try {
                $post_id = $request->get_param('id');
                $deleted = wp_delete_post($post_id, true);
                if ($deleted) {
                    return new WP_REST_Response(array('status' => 'Post deleted'), 200);
                }
                return new WP_Error('delete_failed', 'Failed to delete post', array('status' => 500));
            } catch (Exception $e) {
                return new WP_Error('exception', $e->getMessage(), array('status' => 500));
            }
        }

        /**
         * Summary of get_post
         * Get a post
         * @param mixed $data
         * @return WP_Error|WP_REST_Response
         */
        public function get_item($request)
        {
            try {

                $post_id = $request->get_param('id');
                $post = get_post($post_id);

                if (empty($post)) {
                    return new WP_Error('post_not_found', 'Post not found', array('status' => 404));
                }
                return new WP_REST_Response($post, 200);
            } catch (Exception $e) {
                return new WP_Error('exception', $e->getMessage(), array('status' => 500));
            }
        }
    }
