# GitHub Copilot Onboarding Guide for Laravel Slim

## Repository Overview

This repository (`invokable/laravel-slim`) is a Laravel package designed to transform new Laravel projects into streamlined, purpose-built applications. The package provides two primary use cases:

1. **Console-only applications** - Removes web-related components, keeping only Artisan console functionality
2. **Stateless API applications** - Sets up Sanctum-based API authentication with minimal overhead

### Key Purpose
The package eliminates unnecessary Laravel components for specialized use cases, creating "slim" installations that are lighter and more focused on specific functionality.

## Repository Structure

### Core Components

```
src/
├── SlimServiceProvider.php          # Main service provider - registers Artisan commands
├── Console/
│   ├── SlimConsoleCommand.php       # Command for console-only setup
│   ├── SlimApiCommand.php          # Command for API-only setup with Sanctum
│   ├── Concerns/
│   │   ├── WithCheck.php           # Validation trait - ensures new project safety
│   │   └── WithDelete.php          # File deletion utilities
│   └── stubs/
│       └── api/
│           ├── auth.php            # Sanctum authentication routes template
│           └── ExampleTest.php     # API test template
```

### Supporting Files

- `composer.json` - Package configuration with PSR-4 autoloading under `Revolution\Slim\` namespace
- `pint.json` - Laravel Pint configuration for code style enforcement
- `phpunit.xml` - PHPUnit testing configuration
- `tests/` - Orchestra Testbench-based package tests with Laravel skeleton simulation

## Coding Standards and Conventions

### PHP Standards
- **Strict typing**: All PHP files use `declare(strict_types=1)`
- **Code style**: Laravel Pint with "laravel" preset (configured in `pint.json`)
- **Namespace**: All classes under `Revolution\Slim\` namespace
- **Inheritance**: Commands extend `Illuminate\Console\Command`
- **Traits**: Shared functionality organized in `Concerns/` directory

### File Organization Patterns
- Console commands in `src/Console/`
- Shared traits in `src/Console/Concerns/`
- Template files in `src/Console/stubs/`
- Feature tests in `tests/Feature/`

### Laravel-Specific Conventions
- Uses Laravel's `File` facade for file operations
- Implements `collect()` helper for array operations
- Service provider auto-discovery via `composer.json` extra section
- Artisan command registration in service provider's `boot()` method

### Safety Mechanisms
- **Validation checks**: Both commands verify they're running on new Laravel projects
- **Comprehensive file detection**: Checks for existing modifications/packages before proceeding
- **Detailed output**: Commands provide verbose feedback about operations
- **Rollback protection**: Commands refuse to run on modified projects

## Important Files and Directories

### Critical Files to Understand

1. **`src/SlimServiceProvider.php`**
   - Entry point for the package
   - Registers the two main Artisan commands
   - Only loads commands in console environment

2. **`src/Console/SlimConsoleCommand.php`**
   - Removes: HTTP layer, Models, database, public assets, resources, frontend tooling
   - Keeps: Console commands, basic Laravel core
   - Signature: `slim:console`

3. **`src/Console/SlimApiCommand.php`**
   - Removes: Web routes, frontend assets
   - Adds: Sanctum API authentication, API routes
   - Modifies: User model to include HasApiTokens trait
   - Signature: `slim:api`

4. **`src/Console/Concerns/WithCheck.php`**
   - Validates project state before modifications
   - Prevents accidental runs on existing projects
   - Checks for Breeze, Jetstream, API installations

### Files Deleted by Commands

**Console Command Removes:**
```
app/Http/           # Entire HTTP layer
app/Models/         # Database models
database/           # Migrations, seeders, factories
public/             # Web assets
resources/          # Views, frontend assets
node_modules/       # NPM dependencies
routes/web.php      # Web routes
package.json        # NPM configuration
vite.config.js      # Frontend build tool
postcss.config.js   # CSS processing
tailwind.config.js  # CSS framework
config/{auth,cache,database,filesystems,logging,mail,queue,services,session}.php
```

**API Command Removes:**
```
resources/          # Views and frontend assets
node_modules/       # NPM dependencies
routes/web.php      # Web routes (keeps API routes)
package.json        # NPM configuration
vite.config.js      # Frontend build tool
postcss.config.js   # CSS processing
tailwind.config.js  # CSS framework
```

## GitHub Copilot Best Practices

### Effective Prompting Strategies

#### 1. Context-Aware Prompts
When working with this codebase, provide Copilot with specific context:

```php
// Good prompt context
// Create a new slim command that removes only frontend assets while keeping database functionality
class SlimFrontendCommand extends Command
{
    // Copilot will understand the pattern from existing commands
```

#### 2. Leverage Existing Patterns
Reference existing code patterns in your prompts:

```php
// Reference existing traits
// Create a new concern trait similar to WithDelete but for backing up files
trait WithBackup
{
    // Copilot will follow the established pattern
```

#### 3. Specific File Operations
Be explicit about Laravel file operations:

```php
// Instead of: "delete these files"
// Use: "use Laravel File facade to delete these paths with error handling"
collect([
    base_path('example.json'),
    app_path('Models/Example.php'),
])->each(fn (string $path) => $this->delete($path));
```

### Customizing Suggestions

#### 1. Namespace Consistency
Always specify the correct namespace in prompts:
```php
// Specify namespace for new classes
// Create a new command in Revolution\Slim\Console namespace
namespace Revolution\Slim\Console;
```

#### 2. Laravel Version Targeting
Mention Laravel version compatibility:
```php
// For Laravel 12+ compatibility, create a command that uses the new Application::configure syntax
```

#### 3. Testing Patterns
Reference the existing test structure:
```php
// Create a test following the pattern in SlimTest.php with Orchestra Testbench
class NewFeatureTest extends TestCase
{
    // Copilot will use the established testing patterns
```

### Common Pitfalls to Avoid

#### 1. File Path Confusion
- **Problem**: Mixing absolute and Laravel helper paths
- **Solution**: Always use Laravel path helpers (`base_path()`, `app_path()`, etc.)
- **Prompt**: "Use Laravel path helpers for file operations"

#### 2. Safety Check Bypass
- **Problem**: Creating commands without validation checks
- **Solution**: Always include the WithCheck trait
- **Prompt**: "Include safety validation using WithCheck trait pattern"

#### 3. Service Provider Registration
- **Problem**: Forgetting to register new commands
- **Solution**: Update SlimServiceProvider when adding commands
- **Prompt**: "Add command registration to SlimServiceProvider following existing pattern"

#### 4. Strict Typing Omission
- **Problem**: Missing `declare(strict_types=1)`
- **Solution**: Always include strict typing declaration
- **Prompt**: "Create PHP class with strict typing enabled"

### Copilot-Specific Tips

#### 1. Multi-File Context
When working on related files, open them simultaneously for better context:
- Open command file + corresponding test
- Open service provider when adding new commands
- Open trait files when implementing shared functionality

#### 2. Comment-Driven Development
Use descriptive comments to guide Copilot:

```php
/**
 * Create a new slim command that removes specific Laravel components
 * while preserving console functionality and database connectivity.
 * Should include validation checks and detailed progress output.
 */
class SlimCustomCommand extends Command
{
    // Copilot will generate appropriate implementation
}
```

#### 3. Test-First Approach
Describe test scenarios to generate both tests and implementation:

```php
// Test: Command should fail when routes/api.php already exists
// Test: Command should successfully remove specified directories
// Test: Command should preserve database migrations
public function test_custom_command_behavior()
{
    // Copilot will generate appropriate test logic
}
```

#### 4. Configuration Awareness
Reference existing configuration files:

```php
// Following pint.json configuration, ensure code follows Laravel preset
// Exclude test skeleton files from linting like in existing config
```

### Advanced Prompting Techniques

#### 1. Pattern Extraction
```php
// Extract the file deletion pattern from SlimConsoleCommand and apply it to handle config files only
```

#### 2. Incremental Development
```php
// Extend WithCheck trait to include validation for custom package installations
```

#### 3. Error Handling Enhancement
```php
// Add comprehensive error handling to file operations following Laravel best practices
```

## Development Workflow

### 1. Pre-Development
- Run `composer install` to ensure dependencies are available
- Check existing code style with `./vendor/bin/pint --test`
- Run tests to establish baseline: `./vendor/bin/phpunit`

### 2. During Development
- Use Laravel Pint for consistent formatting
- Write tests first using Orchestra Testbench pattern
- Validate changes don't break existing functionality
- Use descriptive commit messages

### 3. Testing Strategy
- Test both success and failure scenarios
- Use temporary Laravel skeleton for realistic testing
- Verify file operations work correctly
- Test command output and error messages

## Integration with Existing Tools

### Laravel Pint Integration
```bash
# Check code style before committing
./vendor/bin/pint --test

# Auto-fix style issues
./vendor/bin/pint
```

### PHPUnit Integration
```bash
# Run all tests
./vendor/bin/phpunit

# Run with detailed output
./vendor/bin/phpunit --testdox
```

### Composer Scripts
The package follows standard Composer practices. Consider adding these to your workflow:
```bash
# Install dependencies
composer install

# Check for security vulnerabilities
composer audit
```

This guide should help you effectively use GitHub Copilot while maintaining the high standards and patterns established in the Laravel Slim codebase.