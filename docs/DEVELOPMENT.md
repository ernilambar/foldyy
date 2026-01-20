# Development

## Code Quality

The project uses PHPCS and PHPCBF for code quality:

```bash
# Check code style
composer lint

# Fix code style issues
composer format

# Run tests
composer test
```

## Testing

The project uses PHPUnit for unit testing:

```bash
# Run all tests
composer phpunit

# Or directly with PHPUnit
vendor/bin/phpunit

# Run specific test file
vendor/bin/phpunit tests/Unit/CoreUtilsTest.php

# Run with coverage
vendor/bin/phpunit --coverage-html coverage/
```
