<?php
const REST_ROUTE_NAMESPACE = 'lala/v1';
const REST_ROUTE_NAME = 'tiles';
const CACHE_FILE_PATH = '/cache/tiles.json';

# Enable auto-updates for all plugins
add_filter( 'auto_update_plugin', '__return_true' );

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
        'callback' => 'get_cached_tiles',
        'permission_callback' => '__return_true',
    ]);

    // Enable gzip compression for the custom endpoint
    if (ob_start('ob_gzhandler')) {
        ob_start();
    }
});

// Retrieve the custom fields for all 'tile' custom post type entries
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

    return $data;
}

# Implement caching for the custom REST API endpoint with a JSON file
function get_cached_tiles() {
    $file_path = wp_upload_dir()['basedir'] . CACHE_FILE_PATH;

    if (file_exists($file_path)) {
        return json_decode(file_get_contents($file_path), true);
    }

    return set_cached_tiles();
}

function set_cached_tiles() {
    $file_path = wp_upload_dir()['basedir'] . CACHE_FILE_PATH;
    $data = get_custom_tile_fields();

    // Create the cache directory if it doesn't exist
    if (!file_exists(dirname($file_path))) {
        mkdir(dirname($file_path), 0755, true);
    }

    file_put_contents($file_path, json_encode($data));
    return $data;
}

// Hook to recreate the cache JSON file when a 'tile' post type is updated
add_action('wp_after_insert_post', function ($post_id, $post, $update) {
    if ($post->post_type !== 'tile') {
        return;
    }

    set_cached_tiles();
}, 10, 3);

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

