# AST Code MVC PHP Framework Documentation

## Introduction

Welcome to the **AST Code MVC PHP Framework**—a robust, lightweight, and highly customizable MVC (Model-View-Controller) framework designed to accelerate your PHP web development process. Whether you're building a simple website or a complex web application, AST Code MVC provides the essential tools and structure to streamline your workflow, ensuring scalability, maintainability, and security.

## Why Choose AST Code MVC?

- **Lightweight & Fast:** Minimal overhead ensures swift performance without compromising on functionality.
- **Customizable:** Tailor the framework to fit your unique project requirements with ease.
- **Secure:** Built-in security features like CSRF protection, input validation, and password hashing safeguard your applications.
- **Flexible Routing:** Effortlessly define and manage your application's routes.
- **Extensible:** Modular architecture allows for seamless integration of additional features and third-party packages.
- **Developer-Friendly:** Intuitive structure and comprehensive documentation make it easy to onboard and work efficiently.

## Key Features

- **Authentication:** Comprehensive user management system including registration, login, logout, and profile management.
- **Form Handling:** Advanced form creation and validation mechanisms to ensure data integrity.
- **Database Integration:** Robust interaction with MySQL databases using PDO, complete with a custom migration system.
- **Middleware Support:** Implement custom middleware for tasks like authentication checks and CSRF protection.
- **CSRF Protection:** Automatically protect your forms from Cross-Site Request Forgery attacks.
- **Flash Messages:** Provide immediate feedback to users with customizable flash messages.
- **Error Handling:** Custom exception classes and user-friendly error pages enhance the user experience.
- **Routing:** Efficiently define and manage GET and POST routes with clear mappings to controller actions.
- **View Templating:** Consistent UI with layout templates and dynamic content rendering.
- **Session Management:** Secure handling of user sessions to maintain state and manage user data.

## Architecture Overview

The AST Code MVC framework adheres to the MVC design pattern, ensuring a clear separation of concerns:

- **Model:** Represents the data and business logic. Handles data validation and interactions with the database.
- **View:** Renders the user interface. Utilizes layout templates for consistent design across pages.
- **Controller:** Manages the flow between Models and Views. Handles incoming requests, processes data, and returns responses.

Additionally, the framework includes core components such as routing, middleware, and database management to provide a comprehensive development environment.

## Getting Started

### 1. Installation

#### Requirements

- **PHP:** Version 8.0 or higher
- **MySQL:** Version 5.7 or higher
- **Composer:** Dependency management tool for PHP

#### Setup Steps

1. **Clone the Repository:**
   ```bash
   git clone https://github.com/yourusername/astcode-mvcframework.git
   ```
2. **Navigate to the Project Directory:**
   ```bash
   cd astcode-mvcframework
   ```
3. **Install Dependencies via Composer:**
   ```bash
   composer install
   ```
4. **Configure Environment Variables:**
   - Duplicate the `.env.example` file and rename it to `.env`.
   - Update the `.env` file with your database credentials and other configurations.
   ```env
   DB_DSN=mysql:host=localhost;dbname=ast_base_framework
   DB_USER=your_db_username
   DB_PASS=your_db_password
   ```
5. **Run Database Migrations:**
   ```bash
   php Migrations.php
   ```
6. **Set Up Web Server:**
   - Ensure the `public/` directory is set as the document root.
   - For development purposes, you can use PHP's built-in server:
     ```bash
     php -S localhost:8000 -t public
     ```
7. **Access the Application:**
   - Open your browser and navigate to `http://localhost:8000`.

### 2. Defining Routes

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

### 3. Creating Controllers

Controllers handle specific groups of actions and extend the base `Controller` class.

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

### 4. Creating Models

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

### 5. Creating Views

Views are PHP templates that render HTML content. They can extend layout templates for consistent design.

```php:file:Views/example/index.php
<?php /** @var $this \App\Core\View */ ?>
<h1><?= $this->title ?></h1>
<p>Welcome to the example page.</p>
```

### 6. Form Handling

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

### 7. Authentication

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

### 8. Middleware

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
        if (Application::isGuest()) {
            Application::$app->response->redirect('/login');
            exit;
        }
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

## Detailed Architecture

### 1. Core Components

#### Application (`Core/Application.php`)

The `Application` class initializes and manages the core components of the framework, such as routing, database connections, sessions, and views. It acts as the central hub that orchestrates the application's flow.

- **Properties:**
  - `public static Application $app`
  - `public Router $router`
  - `public Request $request`
  - `public Response $response`
  - `public Session $session`
  - `public Database $db`
  - `public View $view`
  - `public ?Controller $controller`
  
- **Methods:**
  - `__construct($rootPath, $config)`: Initializes core components based on the provided configuration.
  - `run()`: Starts the application by resolving incoming requests and sending responses.

#### Router (`Core/Router.php`)

Manages the application's routing system, mapping URLs to corresponding controller actions.

- **Properties:**
  - `protected Request $request`
  - `public Response $response`
  - `protected array $routes`
  
- **Methods:**
  - `__construct($request, $response)`: Initializes the router with request and response objects.
  - `get($path, $callback)`: Defines a route that responds to GET requests.
  - `post($path, $callback)`: Defines a route that responds to POST requests.
  - `resolve()`: Determines the appropriate controller and action based on the current request.

#### Controller (`Core/Controller.php`)

Base controller class that provides common functionalities to all controllers, such as rendering views and managing layouts.

- **Properties:**
  - `public string $layout`
  - `public string $action`
  - `protected array $middlewares`
  
- **Methods:**
  - `setLayout($layout)`: Sets the layout template for the controller.
  - `render($view, $params = [])`: Renders a specific view with optional parameters.
  - `registerMiddleware(BaseMiddleware $middleware)`: Registers middleware to be executed before controller actions.
  - `getMiddlewares(): array`: Retrieves the list of registered middleware.
  - `setFlash($key, $message)`: Sets a flash message to be displayed to the user.

#### Model (`Core/Model.php`)

Abstract base class for all models, handling data validation, rules definition, and interaction with the database.

- **Properties:**
  - `public array $errors`
  
- **Methods:**
  - `loadData($data)`: Loads data into the model's attributes.
  - `validate(): bool`: Validates the data against defined rules.
  - `save(): bool`: Saves the model data to the database.
  - `rules(): array`: Defines validation rules for the model.
  - `labels(): array`: Provides human-readable labels for attributes.
  - `findOne($where)`: Retrieves a single record from the database based on criteria.
  
#### View (`Core/View.php`)

Handles the rendering of view templates and integrates them with layout templates for consistent UI.

- **Properties:**
  - `public string $title`
  
- **Methods:**
  - `renderView($view, $params = [])`: Renders a view within the layout.
  - `renderContent($viewContent)`: Renders raw content within the layout.
  - `layoutContent()`: Retrieves the content of the layout template.
  - `renderOnlyView($view, $params)`: Renders a view without the layout.

#### Database (`Core/DB/Database.php`)

Manages database connections and handles migrations for database schema changes.

- **Properties:**
  - `public \PDO $pdo`
  
- **Methods:**
  - `__construct(array $config)`: Establishes a PDO connection based on configuration.
  - `applyMigrations()`: Applies pending migrations to update the database schema.
  - `createMigrationsTable()`: Creates a table to track applied migrations.
  - `getAppliedMigrations()`: Retrieves a list of migrations that have already been applied.
  - `saveMigrations(array $newMigrations)`: Records new migrations as applied.
  - `log($message)`: Logs messages to the console for debugging and tracking.

### 2. Middleware

Middleware allows for the execution of code before or after controller actions. Common uses include authentication, logging, and security enhancements.

#### AuthMiddleware (`Core/Middlewares/AuthMiddleware.php`)

Ensures that only authenticated users can access certain actions or controllers.

- **Properties:**
  - `public array $actions`
  
- **Methods:**
  - `__construct(array $actions = [])`: Initializes middleware with specific actions it should guard.
  - `execute()`: Checks if the user is authenticated before allowing access to the protected actions.

#### CsrfMiddleware (`Core/Middlewares/CsrfMiddleware.php`)

Protects against Cross-Site Request Forgery by validating CSRF tokens in form submissions.

- **Methods:**
  - `generateCsrfToken()`: Generates a unique CSRF token and stores it in the session.
  - `isValidCsrfToken($token)`: Validates the provided CSRF token against the session.
  - `execute()`: Validates the CSRF token for incoming POST requests.

### 3. Form Handling

The framework provides a robust system for creating and managing forms, ensuring that data is validated and secure.

#### Form Class (`Core/Form/Form.php`)

Facilitates the creation of HTML forms and their corresponding fields.

- **Methods:**
  - `begin($action, $method)`: Starts the form with specified action and method.
  - `end()`: Closes the form.
  - `field($model, $attribute)`: Generates a form field for a specific model attribute.
  
#### Form Fields

Various form field classes extend the base `BaseField` class, providing specific input types like text, email, password, and textarea.

- **BaseField.php**
- **InputField.php**
- **PasswordField.php**
- **TextAreaField.php**
- **SubmitButton.php**

### 4. Authentication System

Handles user registration, login, logout, and profile management, ensuring secure access to protected resources.

#### User Model (`Models/User.php`)

Represents the user entity, including attributes like username, password, email, and status.

- **Methods:**
  - `save()`: Hashes the password and saves the user to the database.
  - `findByPasswordResetToken($token)`: Retrieves a user based on a password reset token.
  - `resetPassword($password)`: Updates the user's password securely.

#### AuthController (`Controllers/AuthController.php`)

Manages authentication-related actions.

- **Methods:**
  - `login(Request $request, Response $response)`: Handles user login.
  - `register(Request $request)`: Manages user registration.
  - `logout(Request $request, Response $response)`: Logs out the user.
  - `profile()`: Displays and manages the user's profile.

### 5. CSRF Protection

Ensures that all form submissions are protected against CSRF attacks.

- **Implementation:**
  - **Token Generation:** A unique CSRF token is generated and embedded in each form.
  - **Token Validation:** Middleware validates the token on form submission to ensure authenticity.

### 6. Database Migrations

A custom migration system allows for version-controlled changes to the database schema.

#### Migration Classes (`migrations/`)

Each migration is a PHP class that defines `up()` and `down()` methods to apply and revert changes.

- **Example Migration:**
  
  ```php:file:migrations/m0002_add_password_column_to_users_table.php
  <?php

  class m0002_add_password_column_to_users_table
  {
      public function up()
      {
          $db = \App\Core\Application::$app->db;
          $SQL = "ALTER TABLE users ADD COLUMN password VARCHAR(64) NOT NULL;";
          $db->pdo->exec($SQL);
      }

      public function down()
      {
          $db = \App\Core\Application::$app->db;
          $SQL = "ALTER TABLE users DROP COLUMN password;";
          $db->pdo->exec($SQL);
      }
  }
  ```

### 7. Security Best Practices

The framework incorporates several security measures to protect web applications:

- **Input Validation:** Ensures that all user inputs meet defined criteria before processing.
- **Password Hashing:** Uses secure algorithms like BCRYPT to hash user passwords.
- **Session Management:** Handles sessions securely to prevent hijacking and fixation.
- **Error Handling:** Custom exception classes prevent the exposure of sensitive information.

## Extending the Framework

AST Code MVC is designed to be extensible. You can add new features, integrate third-party packages, or customize existing components to fit your project's needs.

- **Adding New Middleware:** Create a new middleware class extending `BaseMiddleware` and register it in your controllers.
- **Creating Custom Form Fields:** Extend the `BaseField` class to create custom form input types.
- **Integrating Third-Party Packages:** Utilize Composer to manage and integrate additional libraries as needed.

## Testing

Implement unit and integration tests to ensure the reliability and stability of your application.

- **PHPUnit:** Utilize PHPUnit for writing and running tests.
- **Test Structure:** Organize tests in a `tests/` directory, mirroring the application's structure.
- **Running Tests:**
  ```bash
  ./vendor/bin/phpunit
  ```

## Contribution Guidelines

We welcome contributions from the community! To contribute:

1. **Fork the Repository**
2. **Create a Feature Branch**
   ```bash
   git checkout -b feature/YourFeatureName
   ```
3. **Commit Your Changes**
   ```bash
   git commit -m "Add Your Feature"
   ```
4. **Push to Your Fork**
   ```bash
   git push origin feature/YourFeatureName
   ```
5. **Submit a Pull Request**

Please ensure your code adheres to the project's coding standards and include relevant tests.

## Support

If you encounter any issues or have questions, feel free to open an issue in the repository or reach out to the maintainer at [astcode@users.noreply.github.com](mailto:astcode@users.noreply.github.com).

## License

This project is licensed under the [MIT License](./LICENSE).

---
