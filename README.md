# Lala Headless WordPress

This is a headless WordPress setup using a custom headless theme theme and the `Secure Custom Fields` plugin. It uses DDEV for local development and provides the data for [dideldumthecat/lala-website-react](https://github.com/dideldumthecat/lala-website-react) through the REST API.

## Prerequisites

- [DDEV](https://ddev.readthedocs.io/en/stable/) installed on your machine
- Docker installed and running

## Setup

1. Clone the repository:

    ```sh
    git clone https://github.com/yourusername/lala-headless-wordpress.git
    cd lala-headless-wordpress
    ```

2. Start the DDEV environment:

    ```sh
    ddev start
    ```

3. Import the database (if you have a database dump):

    ```sh
    ddev import-db --src=path/to/your/database.sql.gz
    ```

4. Access the site:

    ```sh
    ddev launch
    ```

## Custom REST API Endpoint

The project includes a custom REST API endpoints for the `tile` custom post type: `/wp-json/lala/v1/tiles`. This is used by [dideldumthecat/lala-website-react](https://github.com/dideldumthecat/lala-website-react) for providing CMS-based content.


## Caching

The project implements caching for the custom REST API endpoint using a JSON file. The cache is recreated whenever a `tile` post type is updated in the WordPress backend.

GZIP compression is also enabled for the REST API response.