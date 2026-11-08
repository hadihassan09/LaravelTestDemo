# Task Manager

A small task manager built with Laravel 13 and Filament 5, using MySQL for storage.

You can create, edit and delete tasks, and reorder them by dragging rows in the table. The order is saved as the task priority: the task at the top is #1, the one below it is #2, and so on. Tasks can also belong to a project. There's a Projects screen for managing them, and a project dropdown above the tasks table to show only that project's tasks.

The whole UI is a Filament panel served from the site root, so `/` takes you to the login page and then to the task list.

## Requirements

- PHP 8.3 or newer, with the extensions Laravel needs plus `intl` and `pdo_mysql`
- Composer 2
- MySQL 8

Node isn't needed. Filament ships its own compiled assets, and they get published into `public/` when you run `composer install`.

## Local setup

Clone the repo and install the PHP dependencies:

```bash
git clone <repository-url> task-manager
cd task-manager
composer install
```

Create your `.env` file and generate the app key:

```bash
cp .env.example .env
php artisan key:generate
```

On Windows (cmd) use `copy .env.example .env` instead of `cp`.

Create an empty MySQL database:

```sql
CREATE DATABASE task_manager CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

Then update the database settings in `.env` to match your MySQL server:

```dotenv
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=task_manager
DB_USERNAME=root
DB_PASSWORD=
```

Run the migrations and seed the demo user:

```bash
php artisan migrate --seed
```

Start the dev server:

```bash
php artisan serve
```

Open http://localhost:8000 and log in with the demo account:

| Email              | Password   |
| ------------------ | ---------- |
| demo@example.com   | password   |

## How priority works

- A new task always goes to the bottom of the list.
- Click the reorder button (the arrows icon above the table), drag rows into place, then click it again to finish. Priorities are rewritten from top to bottom as you drop.
- Deleting a task moves everything below it up by one, so there are no gaps.
- Priority is one list across all tasks. When you pick a project in the dropdown, its tasks keep their overall numbers (you might see #2, #5, #6). Reordering in that view only shuffles those tasks between the positions they already hold, so tasks in other projects don't move.
- Deleting a project doesn't delete its tasks. They stay in the list with no project.

## Deployment

These steps assume a typical Linux server with PHP-FPM behind Nginx or Apache.

1. Get the code onto the server and install dependencies without the dev packages:

   ```bash
   composer install --no-dev --optimize-autoloader
   ```

2. Create `.env` from `.env.example` and set it up for production. Run `php artisan key:generate` on the first deploy only, because changing the key later logs everyone out.

   ```dotenv
   APP_ENV=production
   APP_DEBUG=false
   APP_URL=https://your-domain.com

   DB_HOST=...
   DB_DATABASE=...
   DB_USERNAME=...
   DB_PASSWORD=...
   ```

3. Run the migrations. `--force` is required in production:

   ```bash
   php artisan migrate --force
   ```

4. Cache config, routes, views and events. This also runs `filament:optimize`, which caches Filament's components and icons:

   ```bash
   php artisan optimize
   ```

   Run it again after every deploy. If you change `.env` later, run `php artisan optimize:clear` and then `php artisan optimize`.

5. Point the web server's document root at the `public/` directory, not the project root. For Nginx that's `root /var/www/task-manager/public;`, and for Apache it's `DocumentRoot /var/www/task-manager/public`.

6. Make sure the web server user can write to `storage/` and `bootstrap/cache/`:

   ```bash
   sudo chown -R www-data:www-data storage bootstrap/cache
   sudo chmod -R 775 storage bootstrap/cache
   ```

7. Create a user to log in with. The demo seeder is only meant for local use.

   ```bash
   php artisan make:filament-user
   ```

   It asks for a name, email and password. Every user can access the panel.
