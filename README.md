# Polyglot Portfolio - Laravel API

[![Laravel Version](https://img.shields.io/badge/Laravel-v12.x-FF2D20?logo=laravel&logoColor=white)](https://laravel.com) [![PHP Version](https://img.shields.io/badge/PHP-%5E8.2-777BB4?logo=php&logoColor=white)](https://php.net) [![License](https://img.shields.io/badge/License-MIT-blue.svg)](https://opensource.org/licenses/MIT)

A comprehensive backend REST API built with Laravel (PHP) to serve content for the modern personal portfolio frontend. This is one of the three backend implementations demonstrating cross-stack compatibility.

---

## Key Features

*   **Authentication**: Secure login and API token management using Laravel Sanctum.
*   **Content Management API**: Robust CRUD endpoints with form request validation for managing projects, work experiences, and tech stacks.
*   **File Storage**: Seamless integration with Laravel's local/cloud storage system for uploading portfolio assets.

---

## Tech Stack & Libraries

*   **Backend**: PHP (v8.2+) & Laravel (v12.0)
*   **Database**: MySQL / PostgreSQL (Laravel Eloquent)
*   **Caching & Queue**: Redis (via `predis`)
*   **Emailing**: Resend PHP SDK
*   **Authentication**: Laravel Sanctum (v4.0)

---

## Installation & Setup

### Steps
1.  **Install Dependencies**:
    ```bash
    composer install
    ```
2.  **Configure Environment**:
    ```bash
    cp .env.example .env
    ```
    Update the `.env` file with your database connection details.
3.  **Generate Application Key**:
    ```bash
    php artisan key:generate
    ```
4.  **Run Migrations**:
    ```bash
    php artisan migrate
    ```
5.  **Run Service**:
    ```bash
    php artisan serve
    ```

---

## Author

**Churma16**

---

## License

This project is licensed under the [MIT License](LICENSE).
