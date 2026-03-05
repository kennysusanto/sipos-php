# Simple PHP MVC Project

This is a minimal PHP MVC application with login, dashboard, profile, and admin users page features. It uses no external framework but follows basic MVC organization.

## Features

- User login/logout (DB-backed when configured, fallback hardcoded user)
- Protected dashboard page
- Protected profile page
- Admin-only users page (`/users`) showing records from table `user`
- Light/dark mode toggle saved in `localStorage`
- Simple routing via front controller

## Structure

```
index.php           # front controller
.htaccess           # Apache rewrite rules (optional)
app/
  core/             # base Controller/Model/View classes + Database
  controllers/      # Auth, Dashboard, Profile, Users
  models/           # User model
  views/            # login, dashboard, profile, users views
config/             # configuration
public/
  css/style.css
  js/theme.js
.env.example        # environment variable template
```

## Usage

1. Place project in your web server (e.g., `htdocs` or `www`).
   - make `public/` your document root
2. Copy `.env.example` to `.env` and fill DB values:
   - `DB_HOST=127.0.0.1`
   - `DB_PORT=3306`
   - `DB_NAME=your_database`
   - `DB_USER=your_username`
   - `DB_PASS=your_password`
3. Ensure `.htaccess` in `public/` is enabled.
4. Open in browser at `/` or `/public/`, then login.
5. `/users` is accessible only for users with role `admin`.

## Database table expectation

The app reads from MySQL table `user`:

```sql
CREATE TABLE `user` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `username` VARCHAR(100) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL,
  `role` VARCHAR(50) NOT NULL DEFAULT 'user'
);
```

`password` can be plain text for quick testing, but `password_hash` + `password_verify` is recommended.

Feel free to extend with database support, more controllers, or styling.
