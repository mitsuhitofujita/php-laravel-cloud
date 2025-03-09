# Laravel Project Commands & Guidelines

## Commands
- Build: `npm run build`
- Dev server: `composer run-script dev` (runs Laravel server, queue, logs, and Vite)
- Test all: `ENV=testing php artisan test`
- Test single: `ENV=testing php artisan test --filter=TestName`
- Test class: `ENV=testing php artisan test --filter=ExampleTest`
- Lint code: `./vendor/bin/pint`
- Fix lint: `./vendor/bin/pint --fix`

## Code Style
- PSR-4 autoloading standard for namespaces
- PHP 8.2+, Laravel 12
- 4-space indentation (except YAML: 2 spaces)
- UTF-8 encoding, LF line endings
- camelCase for methods and variables, PascalCase for classes
- Use type hints and return types for methods
- Wrap exceptions in specific application exceptions
- Follow Laravel conventions for controller, model, and service organization
- Use Laravel's validators for input validation
- Use dependency injection when possible

## Standards
- Test coverage expected for new features
- Validate user input at controller level
- Use Laravel's built-in security features
