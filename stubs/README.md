# Ultra-high Quality Laravolt Application

## Getting Started

> **Requires [PHP 8.4+](https://php.net/releases/)**.

Create your type-safe Laravel application using [Composer](https://getcomposer.org):

```bash
composer create-project qisthidev/laravolt-starter-kit --prefer-dist example-app
```

### Initial Setup

Navigate to your project and complete the setup:

```bash
cd example-app

# Setup project
composer setup

# Start the development server
composer dev
```

### Optional: Browser Testing Setup

If you plan to use Pest's browser testing capabilities:

```bash
npm install playwright
npx playwright install
```

### Verify Installation

Run the test suite to ensure everything is configured correctly:

```bash
composer test
```

You should see 100% test coverage and all quality checks passing.

## Available Tooling

### Development

- `composer dev` - Starts Laravel server, queue worker, log monitoring, and Vite dev server concurrently

### Code Quality

- `composer lint` - Runs Rector (refactoring), Pint (PHP formatting), and Prettier (JS/TS formatting)
- `composer test:lint` - Dry-run mode for CI/CD pipelines

### Testing

- `composer test:type-coverage` - Ensures 100% type coverage with Pest
- `composer test:types` - Runs PHPStan at level 9 (maximum strictness)
- `composer test:unit` - Runs Pest tests with 100% code coverage requirement
- `composer test` - Runs the complete test suite (type coverage, unit tests, linting, static analysis)

### Maintenance

- `composer update:requirements` - Updates all PHP and NPM dependencies to latest versions
