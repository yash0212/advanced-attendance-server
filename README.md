# Advanced Attendance System Server

This server provides API for advanced attendance system android app

## Requirements

PHP 7.4

Composer (systemwide preferred)

MySQL

phpMyAdmin

## Installation

1. Run in command line or terminal `composer install`
2. Create a `.env` file or rename `.env.example` to `.env` in the root of the project.
3. Add database credentials and database name in `.env` file
4. Execute the command `php artisan migrate`.
5. Execute the command `php artisan passport:install`.
6. Change the `Document Root` of the webserver to point towards `public` directory of the project
7. Give proper permissions to the project.
8. Restart the server (Optional)
