# Copilot Instructions for projectApara

## Project Overview
This is a **Laravel 12 employee management application** with two distinct employee types: SLECIC and Bank employees. It uses Laravel for the backend, Vite + Tailwind CSS for the frontend, and MySQL for data storage.

## Architecture

### Core Structure
- **Controllers**: `app/Http/Controllers/` - AuthController (auth), UserController (registration)
- **Models**: `app/Models/` - User (base auth model), Slecic_employee, Bank_employee, plus supporting models (Designation, Department, Country)
- **Database**: Laravel migrations handle schema; supports multiple employee types with distinct attributes
- **Frontend**: Blade templates (`resources/views/`) + Tailwind CSS + Vite for asset bundling

### Key Data Models
- **User**: Base authenticatable model (name, email, password) - single users table for both employee types
- **Slecic_employee**: Links to User; includes `desig_id`, `dep_id`, status flag
- **Bank_employee**: Links to User; includes `branch_id`, `role_id`, status string
- **Supporting**: Designation, Department, Country for reference data

### Routing
Routes defined in `routes/web.php`:
- Authentication: `/login` (GET/POST), `/authenticate` (POST)
- Registration: `/slecic-register` (GET), `/slecic-register-confirm` (POST)
- Dashboard: `/dashboard` (protected, requires auth)
- Public views: `/` (login page), `/new-application` (registration page)

## Development Workflow

### Local Setup
```bash
composer install
npm install
php artisan migrate
npm run dev  # Start Vite dev server
php artisan serve  # Start Laravel server
```

### Development Commands
- **Full dev environment**: Run `composer run dev` (uses concurrently to run serve, queue listener, logs, and Vite simultaneously)
- **Vite bundling**: `npm run dev` (watch mode) or `npm run build` (production)
- **Database migrations**: `php artisan migrate` (apply), `php artisan migrate:rollback` (undo)
- **Testing**: `composer run test` (clears config cache then runs PHPUnit)
- **Code style**: `vendor/bin/pint` (Laravel code formatter)

## Project Conventions

### Authentication
- Uses Laravel's built-in `Auth` facade with Eloquent
- Password hashing via `Hash::make()` (automatic casting in User model)
- No email verification currently implemented (commented out in User model)
- Session-based authentication with `session()->regenerate()` pattern

### Validation
- Request validation uses Laravel's fluent syntax in controller methods
- User registration validates: name (required), email (required|email|unique:users), password (required|min:8)
- Common pattern: `$request->validate([...])` returns validated data or throws ValidationException

### Database Patterns
- Migrations use anonymous classes (`return new class extends Migration`)
- Foreign key relationships defined as `integer` columns (e.g., `user_id`, `desig_id`)
- **Note**: Migrations use integer FKs without explicit foreign() constraints - add these if implementing cascade delete

### View Structure
- Entry point views: `login.blade.php`, `application.blade.php`, `dashboard.blade.php`
- SLECIC-specific views in `resources/views/slecic/` subdirectory
- Layouts in `resources/views/layouts/`
- Static assets served from `public/` (includes Bootstrap 5.3.8 distribution)

## Critical Files to Know
- `app/Http/Controllers/AuthController.php` - Auth logic (login, dashboard)
- `app/Http/Controllers/UserController.php` - User registration for SLECIC
- `app/Models/User.php` - Base auth model with fillable fields (name, email, password)
- `routes/web.php` - All HTTP routes (small enough to understand in one read)
- `database/migrations/` - Schema definitions; check date-prefixed files for table structures
- `vite.config.js` - Vite configuration (Tailwind CSS + Laravel plugin)

## Before Implementing Features

1. **Check route existence**: Routes are simple; verify in `web.php` before creating new ones
2. **Model relationships**: Add Eloquent relationships (`belongsTo`, `hasMany`) once ForeignKey migrations are complete
3. **Middleware**: No custom middleware visible; use Laravel's default auth middleware pattern
4. **Blade syntax**: Views use Blade templates (Laravel's templating); use `{{ }}` for variables, `@if/@foreach` for logic
5. **Vite imports**: CSS/JS imported in `resources/css/app.css` and `resources/js/app.js` - these are bundled by Vite

## Known Gaps/Next Steps
- Foreign key relationships in migrations not explicitly defined with `->foreign()` - should add these
- No visible authorization/roles beyond employee type separation (no gate/policy pattern)
- Email verification disabled - needs setup if required
- Consider adding timestamps to all migrations if audit trail needed

## Testing & Debugging
- PHPUnit tests in `tests/` directory (Feature and Unit folders exist)
- Run `composer run test` to execute suite
- Use `php artisan tinker` for interactive shell debugging
- Laravel Pail available for real-time log monitoring (`php artisan pail`)
