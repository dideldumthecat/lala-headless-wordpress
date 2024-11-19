#!/bin/bash

# Enable debugging and logging
#set -x

# Load environment variables from the .env file
set -a
source .env
set +a

${COMPOSER_COMMAND}

wp core download --path=/var/www/html --locale=en_US
wp core install --path=/var/www/html --url="$WP_SITE_URL" --title="$WP_SITE_TITLE" --admin_user="$WP_ADMIN_USER" --admin_password="$WP_ADMIN_PASS" --admin_email="$WP_ADMIN_EMAIL"

# Install the Advanced Custom Fields plugin
wp plugin install advanced-custom-fields

# Remove all other themes except the headless theme
wp theme list --status=inactive --field=name | grep -v "$MY_THEME" | xargs -I {} wp theme delete {}

# Import the database dump
wp db import db-export.sql