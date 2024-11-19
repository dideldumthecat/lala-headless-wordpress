#!/bin/bash

# Load environment variables from the .env file
export $(grep -v '^#' .env | xargs)

# Set WordPress site URL, title, admin username and password from the .env file
WP_SITE_URL="$WP_HOME/"
WP_SITE_TITLE="$WP_SITE_TITLE"
WP_ADMIN_USER="$WP_ADMIN_USER"
WP_ADMIN_PASS="$WP_ADMIN_PASS"
WP_ADMIN_EMAIL="$WP_ADMIN_EMAIL"
MY_THEME="headless-theme"

composer install

wp core download --path=/var/www/html --locale=en_US
wp core install --path=/var/www/html --url="$WP_SITE_URL" --title="$WP_SITE_TITLE" --admin_user="$WP_ADMIN_USER" --admin_password="$WP_ADMIN_PASS" --admin_email="$WP_ADMIN_EMAIL"

# Install the Advanced Custom Fields plugin
wp plugin install advanced-custom-fields

# Remove all other themes except the headless theme
wp theme list --status=inactive --field=name | grep -v "$MY_THEME" | xargs -I {} wp theme delete {}

# Import the database dump
wp db import db-export.sql