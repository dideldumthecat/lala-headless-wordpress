<?php
const REST_ROUTE_NAMESPACE = 'lala/v1';
const REST_ROUTE_NAME = 'tiles';

// Redirect all requests to the homepage
add_action('template_redirect', function() {
    if (
        (!defined('REST_REQUEST') || !REST_REQUEST) && 
        $_SERVER['REQUEST_URI'] !== '/'
    ) {
            wp_redirect(home_url('/'));
        exit;
    }
});

// Register a custom REST API endpoint for the 'tile' custom post type
add_action('rest_api_init', function () {
    register_rest_route(REST_ROUTE_NAMESPACE, '/' . REST_ROUTE_NAME, [
        'methods' => 'GET',
        'callback' => 'get_custom_tile_fields',
        'permission_callback' => '__return_true',
    ]);

    // Enable gzip compression for the custom endpoint
    if (ob_start('ob_gzhandler')) {
        ob_start();
    }
});

// Callback function for fetching custom fields only
function get_custom_tile_fields() {
    $tiles = get_posts([
        'post_type' => 'tile',
        'posts_per_page' => -1,
        'order' => 'ASC',
    ]);

    $data = [];
    
    foreach ($tiles as $tile) {
        $custom_fields = get_fields($tile->ID);  // Fetch all custom fields
        
        $data[] = [
            'title' => get_the_title($tile->ID),
            'acf' => $custom_fields,  // Output only custom fields
        ];
    }

    return rest_ensure_response($data);
}

// Disable all default REST API endpoints except for the custom 'tiles' endpoint
add_filter('rest_endpoints', function ($endpoints) {
    // Check if the request is from the admin area
    if (is_user_logged_in()) {
        return $endpoints;
    }

    // Unset all default endpoints
    foreach ($endpoints as $route => $endpoint) {
        if (strpos($route, REST_ROUTE_NAMESPACE) === false) {
            unset($endpoints[$route]);
        }
    }
    return $endpoints;
});

# Enable auto-updates for all plugins
add_filter( 'auto_update_plugin', '__return_true' );