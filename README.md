# AST Code MVC PHP Framework

![License](https://img.shields.io/badge/license-MIT-blue.svg)
![PHP Version](https://img.shields.io/badge/PHP-8.0%2B-blue.svg)
![Composer](https://img.shields.io/badge/Composer-2.0%2B-blue.svg)

## Table of Contents
1. [Overview](#overview)
2. [Features](#features)
3. [Installation](#installation)
4. [Usage](#usage)
5. [Project Structure](#project-structure)
6. [Documentation](#documentation)
7. [Contributing](#contributing)
8. [License](#license)

---

## Overview

**AST Code MVC PHP Framework** is a lightweight, custom MVC (Model-View-Controller) framework built in PHP. It offers a structured approach to web application development, incorporating essential features such as authentication, form handling, database operations, and more. Designed for simplicity and flexibility, this framework allows developers to build robust web applications without the overhead of larger frameworks.

---

## Features

- **Authentication:** User registration, login, logout, and profile management.
- **Form Handling:** Robust form creation and validation system.
- **Database Operations:** Seamless interaction with MySQL using PDO, including migrations.
- **Middleware Support:** Implement custom middleware for request filtering and security.
- **CSRF Protection:** Protect forms from Cross-Site Request Forgery attacks.
- **Flash Messages:** Provide user feedback messages across requests.
- **Error Handling:** Custom exception classes and error views for better user experience.
- **Routing:** Define and manage application routes efficiently.
- **View Templating:** Utilize layouts and dynamic content rendering for consistent UI.

---

## Installation

### Requirements

Before installing the AST Code MVC PHP Framework, ensure your system meets the following requirements:

- **PHP:** Version 8.0 or higher
- **MySQL:** Version 5.7 or higher
- **Composer:** Dependency management tool for PHP

### Setup Steps

1. **Clone the Repository:**   ```bash
   git clone https://github.com/yourusername/astcode-mvcframework.git   ```
2. **Navigate to the Project Directory:**   ```bash
   cd astcode-mvcframework   ```
3. **Install Dependencies via Composer:**   ```bash
   composer install   ```
4. **Configure Environment Variables:**
   - Duplicate the `.env.example` file and rename it to `.env`.
   - Update the `.env` file with your database credentials and other configurations.   ```env
   DB_DSN=mysql:host=localhost;dbname=ast_base_framework
   DB_USER=your_db_username
   DB_PASS=your_db_password   ```
5. **Run Database Migrations:**   ```bash
   php Migrations.php   ```
6. **Set Up Web Server:**
   - Ensure the `public/` directory is set as the document root.
   - For development purposes, you can use PHP's built-in server:     ```bash
     php -S localhost:8000 -t public     ```
7. **Access the Application:**
   - Open your browser and navigate to `http://localhost:8000`.

---

## Usage

### Defining Routes

Routes are defined in the `public/index.php` file using the `Router` class.

```php:file:public/index.php
$app->router->get('/home', [SiteController::class, 'home']);
$app->router->post('/contact', [SiteController::class, 'contact']);
```

**Example:**

```php:file:public/index.php
$app->router->get('/about', [SiteController::class, 'about']);
$app->router->post('/submit-form', [FormController::class, 'submit']);
```

### Creating Controllers

Controllers handle specific groups of actions. They extend the base `Controller` class.

```php:file:Controllers/ExampleController.php
<?php

namespace App\Controllers;

use App\Core\Controller;

class ExampleController extends Controller
{
    public function index()
    {
        return $this->render('example/index');
    }

    public function submit()
    {
        // Handle form submission
    }
}
```

### Creating Models

Models represent data and business logic. They extend the base `Model` class and define attributes, validation rules, and interaction methods.

```php:file:Models/ExampleModel.php
<?php

namespace App\Models;

use App\Core\Model;

class ExampleModel extends Model
{
    public string $title = '';
    public string $description = '';

    public function rules(): array
    {
        return [
            'title' => [self::RULE_REQUIRED],
            'description' => [self::RULE_REQUIRED]
        ];
    }

    public function save()
    {
        // Logic to save data to the database
    }
}
```

### Creating Views

Views are PHP templates that render HTML content. They can extend layout templates for consistent design.

```php:file:Views/example/index.php
<?php /** @var $this \App\Core\View */ ?>
<h1><?= $this->title ?></h1>
<p>Welcome to the example page.</p>
```

### Form Handling

Utilize the `Form` class to create and manage forms with built-in validation.

```php:file:Views/auth/register.php
<?php
use App\Core\Form\Form;

/** @var $model \App\Models\User */
?>
<?php $form = Form::begin('/register', 'post'); ?>
    <?= $form->field($model, 'firstname')->placeholder('Enter your First Name...'); ?>
    <?= $form->field($model, 'lastname')->placeholder('Enter your Last Name...'); ?>
    <?= $form->field($model, 'username')->placeholder('Choose a Username...'); ?>
    <?= $form->field($model, 'password')->passwordField()->placeholder('Enter your Password...'); ?>
<?php echo Form::end(); ?>
```

### Authentication

The framework provides built-in authentication mechanisms.

- **Register:** Create new user accounts.
- **Login:** Authenticate existing users.
- **Logout:** End user sessions.
- **Profile:** Manage user profiles.

**Example Routes:**

```php:file:public/index.php
$app->router->get('/login', [AuthController::class, 'login']);
$app->router->post('/login', [AuthController::class, 'login']);
```

### Middleware

Implement middleware to handle request filtering, such as authentication checks or CSRF protection.

```php:file:Core/Middlewares/AuthMiddleware.php
<?php

namespace App\Core\Middlewares;

use App\Core\Application;
use App\Core\Controller;

class AuthMiddleware extends BaseMiddleware
{
    public function __construct(array $actions)
    {
        $this->actions = $actions;
    }

    public function execute()
    {
        if (!Application::isGuest()) {
            return;
        }

        Application::$app->response->redirect('/login');
        exit;
    }
}
```

**Registering Middleware in Controller:**

```php:file:Controllers/AuthController.php
<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Middlewares\AuthMiddleware;

class AuthController extends Controller
{
    public function __construct()
    {
        $this->registerMiddleware(new AuthMiddleware(['profile']));
    }

    // Other methods...
}
```

---

## Project Structure

```
Controllers/
┣ AuthController.php
┗ SiteController.php
Core/
┣ DB/
┃ ┣ Database.php
┃ ┗ DbModel.php
┣ Exception/
┃ ┣ ForbiddenException.php
┃ ┗ NotFoundException.php
┣ Form/
┃ ┣ BaseField.php
┃ ┣ Form.php
┃ ┣ InputField.php
┃ ┣ SubmitButton.php
┃ ┗ TextAreaField.php
┣ Middlewares/
┃ ┣ AuthMiddleware.php
┃ ┗ BaseMiddleware.php
┣ .editorconfig
┣ Application.php
┣ Controller.php
┣ functions.php
┣ Model.php
┣ Request.php
┣ Response.php
┣ Router.php
┣ Session.php
┣ UserModel.php
┗ View.php
migrations/
┣ m0001_initial.php
┗ m0002_add_password_column_to_users_table.php
Models/
┣ ContactForm.php
┣ LoginForm.php
┗ User.php
public/
┗ index.php
vendor/
Views/
┣ auth/
┃ ┣ login.php
┃ ┗ register.php
┣ exceptions/
┃ ┣ _404.php
┃ ┗ _error.php
┣ layouts/
┃ ┣ auth.php
┃ ┗ main.php
┣ userpages/
┃ ┗ profile.php
┣ contact.php
┗ home.php
.env
.htaccess
composer.json
composer.lock
Migrations.php
```

### Description

- **Controllers/**: Handles incoming requests and returns responses by interacting with Models and Views.
- **Core/**: Contains the core classes and functionalities of the framework, including routing, middleware, database interactions, and more.
- **migrations/**: Holds database migration scripts for setting up and modifying the database schema.
- **Models/**: Represents the data structure and business logic. Handles data validation and interactions with the database.
- **public/**: The document root where the application is accessible. Contains the `index.php` entry point.
- **vendor/**: Managed by Composer, contains all third-party packages and dependencies.
- **Views/**: Contains all view templates rendered to the user, organized by functionality.
- **.env**: Environment configuration file containing sensitive information and settings.
- **composer.json** & **composer.lock**: Manage project dependencies.

---

## Documentation

For detailed documentation on the AST Code MVC PHP Framework, please refer to [Documentation.md](./Core/Documentation.md).

---

## Contributing

Contributions are welcome! Please follow these steps to contribute to the AST Code MVC PHP Framework:

1. **Fork the Repository:**
   - Click the "Fork" button on the repository page.

2. **Clone Your Fork:**
   ```bash
   git clone https://github.com/yourusername/astcode-mvcframework.git
   ```

3. **Create a Feature Branch:**
   ```bash
   git checkout -b feature/YourFeatureName
   ```

4. **Commit Your Changes:**
   ```bash
   git commit -m "Add Your Feature"
   ```

5. **Push to Your Fork:**
   ```bash
   git push origin feature/YourFeatureName
   ```

6. **Create a Pull Request:**
   - Navigate to your fork on GitHub.
   - Click the "Compare & pull request" button.
   - Provide a descriptive title and detailed description of your changes.
   - Submit the pull request.

Please ensure your contributions adhere to the project's coding standards and include appropriate tests where applicable.

---

## License

This project is licensed under the [MIT License](./LICENSE).

---
