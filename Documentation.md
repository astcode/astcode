# AST Code MVC PHP Framework Documentation

## Table of Contents
1. [Overview](#overview)
2. [Installation](#installation)
   - [Requirements](#requirements)
   - [Setup Steps](#setup-steps)
3. [Project Structure](#project-structure)
4. [Core Components](#core-components)
5. [Features](#features)
6. [Usage Guide](#usage-guide)
   - [Defining Routes](#defining-routes)
   - [Creating Controllers](#creating-controllers)
   - [Creating Models](#creating-models)
   - [Creating Views](#creating-views)
   - [Form Handling](#form-handling)
   - [Authentication](#authentication)
   - [Middleware](#middleware)
7. [Database](#database)
   - [Configuration](#configuration)
   - [Migrations](#migrations)
8. [Security](#security)
9. [API Reference](#api-reference)
10. [Contributing](#contributing)
11. [License](#license)

---

## Overview

**AST Code MVC PHP Framework** is a lightweight, custom MVC (Model-View-Controller) framework built in PHP. It offers a structured approach to web application development, incorporating essential features such as authentication, form handling, database operations, and more. Designed for simplicity and flexibility, this framework allows developers to build robust web applications without the overhead of larger frameworks.

---

## Installation

### Requirements

Before installing the AST Code MVC PHP Framework, ensure your system meets the following requirements:

- **PHP:** Version 8.0 or higher
- **MySQL:** Version 5.7 or higher
- **Composer:** Dependency management tool for PHP

### Setup Steps

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

---

## Project Structure
