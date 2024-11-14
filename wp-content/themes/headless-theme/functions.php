<?php

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