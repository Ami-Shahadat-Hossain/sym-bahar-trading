# SYMB Bahar Trading

A Laravel-based business management application for managing customers, challans, invoices, money receipts, and related trading operations.

## Table of Contents

- [Overview](#overview)
- [Features](#features)
- [Technology Stack](#technology-stack)
- [Requirements](#requirements)
- [Local Installation](#local-installation)
- [Environment Configuration](#environment-configuration)
- [Database Setup](#database-setup)
- [Frontend Assets](#frontend-assets)
- [Running the Application](#running-the-application)
- [GitHub Setup](#github-setup)
- [Wasmer Deployment](#wasmer-deployment)
- [Production Security](#production-security)
- [Troubleshooting](#troubleshooting)
- [Project Structure](#project-structure)
- [Maintenance](#maintenance)
- [License](#license)

## Overview

SYMB Bahar Trading is a web application built with Laravel for managing business operations through a centralized interface.

The application uses Laravel authentication and a Blade-based interface with a custom navigation layout.

## Features

- User authentication and logout
- User profile management
- Profile information updates
- Password updates
- Customer management
- Challan management
- Invoice management
- Money receipt management
- Dashboard statistics
- Recent challan and invoice summaries
- PostgreSQL database support
- Responsive interface
- Production deployment support

Note: Available features depend on the modules and routes implemented in the application.

## Technology Stack

- **Backend:** PHP and Laravel
- **Frontend:** Blade templates, JavaScript, Tailwind CSS
- **Asset Bundler:** Vite
- **Authentication:** Laravel Breeze
- **Database:** PostgreSQL
- **Dependency Management:** Composer and npm
- **Version Control:** Git and GitHub
- **Deployment Target:** Wasmer

## Requirements

Install the following software for local development:

- PHP version compatible with the Laravel version used by this project
- Composer
- Node.js and npm
- PostgreSQL
- Git
- A compatible PHP web server

Required PHP extensions depend on the Laravel version and installed packages. PostgreSQL deployments require the `pdo_pgsql` extension.

## Local Installation

### 1. Clone the repository

```bash
git clone https://github.com/YOUR_USERNAME/sym-bahar-trading-clean.git
cd sym-bahar-trading-clean
```

Replace `YOUR_USERNAME` with your GitHub username.

### 2. Install PHP dependencies

```bash
composer install
```

### 3. Create the environment file

Windows PowerShell:

```powershell
Copy-Item .env.example .env
```

Linux or macOS:

```bash
cp .env.example .env
```

### 4. Generate the application key

```bash
php artisan key:generate
```

For an existing production application, retain its existing `APP_KEY`. Do not regenerate it casually because encrypted data and sessions may depend on it.

### 5. Configure the database

Edit the `.env` file with the appropriate database connection settings.

See [Environment Configuration](#environment-configuration).

### 6. Run database migrations

```bash
php artisan migrate
```

Only run this command against the intended development database.

If your project relies on seeders:

```bash
php artisan db:seed
```

Run seeders only after confirming which records they create and whether they are safe for the target database.

### 7. Install frontend dependencies

```bash
npm install
```

### 8. Build frontend assets

```bash
npm run build
```

### 9. Start the local application

```bash
php artisan serve
```

Open:

http://127.0.0.1:8000

## Environment Configuration

Configure your local `.env` file using values appropriate for your environment.

### PostgreSQL example

```dotenv
APP_NAME="SYMB Bahar Trading"
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://127.0.0.1:8000

LOG_CHANNEL=stack
LOG_LEVEL=debug

DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=your_database_name
DB_USERNAME=your_database_username
DB_PASSWORD=your_database_password

SESSION_DRIVER=database
CACHE_STORE=database
QUEUE_CONNECTION=database
```

This is an example configuration, not a replacement for every setting in your existing `.env` file.

Use the actual credentials for your PostgreSQL installation.

Do not commit `.env` to GitHub.

## Database Setup

### PostgreSQL

Ensure that PostgreSQL is running and the target database exists.

Verify the database connection using:

```bash
php artisan migrate:status
```

To apply pending migrations in development:

```bash
php artisan migrate
```

### Existing database

If your application already has important business data, back up the database before changing schemas or restoring a database dump.

Do not run `migrate:fresh` on a database containing data you need to preserve.

If importing an existing PostgreSQL backup, confirm the backup format and schema before choosing the restore command.

## Frontend Assets

This project uses Vite for frontend asset compilation.

Install packages:

```bash
npm install
```

Build production assets:

```bash
npm run build
```

The generated assets should normally be available under:

```text
public/build/
```

The production build should include:

```text
public/build/manifest.json
```

Ensure the production deployment includes the generated assets or runs the appropriate build process.

## Running the Application

Start Laravel's local development server:

```bash
php artisan serve
```

Run the frontend development server when actively developing CSS or JavaScript:

```bash
npm run dev
```

For production, use the web server and runtime configuration supported by your hosting platform.

Do not use Laravel's development server as a public production server.

## GitHub Setup

### 1. Initialize Git

If the project is not already a Git repository:

```bash
git init
```

### 2. Check sensitive files

Ensure `.gitignore` excludes at least:

```gitignore
.env
.env.*
!.env.example

/vendor/
/node_modules/

/storage/*.key
```

Keep the project's existing Laravel ignore rules as well.

Do not commit:

- Database passwords
- Production `.env` files
- API keys and tokens
- Private keys
- Database backups containing sensitive business data
- Unnecessary logs or generated files

### 3. Stage and commit

```bash
git add .
git status
git commit -m "Prepare Laravel application for deployment"
```

Review the staged files before committing.

### 4. Configure the GitHub repository

Create a private repository on GitHub.

Example repository name:

```text
sym-bahar-trading-clean
```

If the local repository does not have a remote:

```bash
git branch -M main
git remote add origin https://github.com/YOUR_USERNAME/sym-bahar-trading-clean.git
git push -u origin main
```

If `origin` already exists, inspect it:

```bash
git remote -v
```

Update it only if necessary.

### 5. Push future changes

```bash
git add .
git commit -m "Describe your changes"
git push origin main
```

## Wasmer Deployment

Deploying Laravel requires a runtime that supports the application's PHP version, extensions, dependencies, routing, and database connection.

Follow the current Wasmer documentation for the deployment method supported by your application.

Documentation:

- Wasmer: https://wasmer.io/
- Wasmer documentation: https://docs.wasmer.io/
- Laravel documentation: https://laravel.com/docs

### 1. Push the project to GitHub

Confirm that the latest code is available in your private GitHub repository.

### 2. Connect the repository

In Wasmer, configure your application to use the GitHub repository if Git-based deployment is supported by your selected setup.

Select the production branch, normally:

```text
main
```

### 3. Configure the runtime

Verify that the runtime supports:

- The PHP version required by the application
- Composer dependencies
- Required PHP extensions
- PostgreSQL through `pdo_pgsql`
- Laravel's public entry point and request routing
- The frontend production assets

Do not assume that every Wasmer runtime supports a traditional PHP application in the same way as a conventional PHP hosting provider. Follow the applicable runtime guide.

### 4. Configure production environment variables

Set the variables through the hosting platform's secure configuration interface.

Example:

```dotenv
APP_NAME="SYMB Bahar Trading"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://YOUR_PRODUCTION_DOMAIN

DB_CONNECTION=pgsql
DB_HOST=YOUR_POSTGRES_HOST
DB_PORT=5432
DB_DATABASE=YOUR_DATABASE_NAME
DB_USERNAME=YOUR_DATABASE_USERNAME
DB_PASSWORD=YOUR_DATABASE_PASSWORD
```

Also configure:

- `APP_KEY` using the existing application's key
- Session storage
- Cache storage
- Mail settings if email is required
- Any other environment variables used by the application

Replace all example values with actual configuration.

Never publish production secrets in this README or commit them to GitHub.

### 5. Configure PostgreSQL connectivity

The production application must connect to a reachable PostgreSQL database.

A PostgreSQL database running only on your local computer is not automatically available to a deployed application.

Confirm that:

- The database exists
- The credentials are correct
- The database allows connections from the deployment environment
- Network access and TLS requirements are satisfied
- The PHP runtime has `pdo_pgsql` installed

### 6. Install dependencies and build assets

Use the deployment process supported by the selected runtime to install production dependencies and build frontend assets.

Typical commands are:

```bash
composer install --no-dev --prefer-dist --optimize-autoloader
npm ci
npm run build
```

These commands are examples for a compatible build environment. Do not assume Node.js or Composer is available in the final runtime unless the platform provides it.

If the deployment uses separate build and runtime stages, ensure that the required vendor files and compiled assets are included in the final artifact.

### 7. Run production migrations

After verifying the database connection and taking any necessary backup, run:

```bash
php artisan migrate --force
```

Run migrations only against the intended production database.

Use a deployment step or secure administrative environment that has access to the production database.

### 8. Clear and rebuild caches

After environment variables and deployment configuration are correct, use supported deployment commands such as:

```bash
php artisan optimize:clear
php artisan config:cache
php artisan view:cache
```

Run these in the correct application environment. If configuration changes, rebuild the configuration cache as part of deployment.

### 9. Test the deployed application

Verify:

- The home page opens over HTTPS
- Login and logout work
- Profile settings open
- Profile updates work
- Database queries succeed
- Customers, challans, invoices, and receipts load correctly
- CSS and JavaScript assets load
- Laravel logs do not show production errors

### 10. Future deployments

After updating the code locally:

```bash
git add .
git commit -m "Update application"
git push origin main
```

If Git-based automatic deployment is configured, Wasmer may deploy the new revision automatically. Otherwise, trigger deployment through the supported Wasmer workflow.

## Production Security

### Application settings

Production should use:

```dotenv
APP_ENV=production
APP_DEBUG=false
```

Do not enable debug output on a public website.

### Protect credentials

- Keep `.env` out of Git.
- Store secrets in the hosting platform's secret manager or environment configuration.
- Use a private GitHub repository for proprietary source code.
- Use strong database credentials.
- Do not expose database ports publicly unless required and properly secured.

### Database safety

- Back up the production database regularly.
- Test migrations before production deployment.
- Avoid destructive migration commands on production.
- Restrict database permissions to those required by the application.

### Sessions and uploads

Confirm that session storage, cache, and uploaded files use storage appropriate for the hosting platform.

Do not assume that files written to an instance's local filesystem will persist across redeployments or instance replacement.

## Troubleshooting

### HTTP 500 — Internal Server Error

Check the Laravel log:

```bash
php artisan pail
```

If Pail is not installed, inspect:

```text
storage/logs/laravel.log
```

Check environment variables, permissions, runtime compatibility, and database connectivity.

Never enable `APP_DEBUG=true` on the public production website to troubleshoot.

### Database connection failure

Verify:

```dotenv
DB_CONNECTION=pgsql
DB_HOST=YOUR_POSTGRES_HOST
DB_PORT=5432
DB_DATABASE=YOUR_DATABASE_NAME
DB_USERNAME=YOUR_DATABASE_USERNAME
DB_PASSWORD=YOUR_DATABASE_PASSWORD
```

Also confirm that the PHP runtime has `pdo_pgsql` enabled and can reach the database.

### Vite manifest missing

Build frontend assets:

```bash
npm install
npm run build
```

Ensure `public/build/manifest.json` is present in the deployed application.

### Profile page is blank

Confirm that:

- The `profile.edit` route exists.
- The profile view uses the correct layout.
- The custom `layouts.app` layout yields `@section('content')`.
- The required profile partials exist.
- The Laravel logs contain no Blade or component errors.

Clear compiled views:

```bash
php artisan view:clear
```

### Route changes are not visible

Run:

```bash
php artisan route:list
php artisan route:clear
php artisan optimize:clear
```

### Permission or cache issues

Use the filesystem and cache permissions appropriate to the runtime. Ensure Laravel can write to the required `storage` and `bootstrap/cache` directories.

Do not apply broad, unsafe permissions to solve deployment problems.

## Project Structure

```text
sym-bahar-trading-clean/
├── app/
│   ├── Http/
│   └── Models/
├── bootstrap/
├── config/
├── database/
│   ├── migrations/
│   └── seeders/
├── public/
│   └── build/
├── resources/
│   ├── css/
│   ├── js/
│   └── views/
│       ├── layouts/
│       └── profile/
├── routes/
├── storage/
├── tests/
├── .env.example
├── .gitignore
├── artisan
├── composer.json
├── package.json
├── phpunit.xml
└── README.md
```

The actual project structure may differ depending on installed packages and implemented modules.

## Maintenance

### Check migrations

```bash
php artisan migrate:status
```

### Clear cached configuration and views

```bash
php artisan optimize:clear
```

### Update PHP dependencies

Review dependency changes before updating:

```bash
composer update
```

Do not run unrestricted dependency updates directly in production. Test changes and commit the updated lock file.

### Update frontend dependencies

```bash
npm install
npm run build
```

Commit `package-lock.json` when dependency changes are intentional.

## License

This project is proprietary unless a separate license explicitly states otherwise.

Do not copy, redistribute, publish, or commercially reuse the source code without authorization from the project owner.
