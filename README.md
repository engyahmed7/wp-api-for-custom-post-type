# Custom Post Type API REST Plugin

## Description

This WordPress plugin registers REST API routes for a custom post type, allowing for CRUD operations via the WordPress REST API.

## Installation

1. Download the plugin files.
2. Upload the plugin folder to the `/wp-content/plugins/` directory.
3. Activate the plugin through the 'Plugins' menu in WordPress.

## Usage

Once activated, the plugin will automatically register REST API routes for the custom post type. You can interact with these routes using standard REST API methods.

## Endpoints

### List Items
- **URL**: `/wp-json/{namespace}/{rest_base}`
- **Method**: GET
- **Callback**: `get_items`
- **Permission Callback**: `get_items_permissions_check`

### Create Item
- **URL**: `/wp-json/{namespace}/{rest_base}`
- **Method**: POST
- **Callback**: `create_item`
- **Permission Callback**: `create_item_permissions_check`
- **Arguments**: Defined by `get_endpoint_args_for_item_schema(true)`

### Get Item by ID
- **URL**: `/wp-json/{namespace}/{rest_base}/(?P<id>\d+)`
- **Method**: GET
- **Callback**: `get_item`
- **Permission Callback**: `get_item_permissions_check`

### Update Item by ID
- **URL**: `/wp-json/{namespace}/{rest_base}/(?P<id>\d+)`
- **Method**: POST
- **Callback**: `update_item`
- **Permission Callback**: `update_item_permissions_check`
- **Arguments**: Defined by `get_endpoint_args_for_item_schema(false)`

### Delete Item by ID
- **URL**: `/wp-json/{namespace}/{rest_base}/(?P<id>\d+)`
- **Method**: DELETE
- **Callback**: `delete_item`
- **Permission Callback**: `delete_item_permissions_check`

## Contributing

1. Fork the repository.
2. Create a new branch (`git checkout -b feature-branch`).
3. Make your changes.
4. Commit your changes (`git commit -am 'Add new feature'`).
5. Push to the branch (`git push origin feature-branch`).
6. Create a new Pull Request.

## License

This plugin is licensed under the [MIT License](LICENSE).