#!/bin/bash

# Enable debugging and logging
#set -x

# Load environment variables from the .env file
set -a
source .env
set +a

${COMPOSER_COMMAND}

wp core download --path=./ --locale=en_US
wp core install --path=./ --url="$WP_SITE_URL" --title="$WP_SITE_TITLE" --admin_user="$WP_ADMIN_USER" --admin_password="$WP_ADMIN_PASS" --admin_email="$WP_ADMIN_EMAIL"

# Install the Advanced Custom Fields and SMTP plugins
wp plugin install advanced-custom-fields --activate
wp plugin install wp-mail-smtp --activate

# Remove all other plugins except the ACF and SMTP plugins
wp plugin list --status=inactive --field=name | grep -v "wp-mail-smtp" | grep -v "advanced-custom-fields" | xargs -I {} wp plugin delete {}

# Remove all other themes except the headless theme
wp theme list --status=inactive --field=name | grep -v "$MY_THEME" | xargs -I {} wp theme delete {}