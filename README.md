# Foldyy

Folder tree viewer with size information - CLI tool to display folder structure with file and folder sizes.

## Description

Foldyy is a command-line tool that displays a tree view of a folder structure with size information for each file and folder. It's useful for quickly understanding the size distribution of files and directories in a given path.

## Installation

```bash
composer install
```

## Usage

```bash
# Basic usage (saves HTML to temp directory)
./bin/foldyy /path/to/folder

# With maximum depth limit
./bin/foldyy /path/to/folder --max-depth 5

# Without size information
./bin/foldyy /path/to/folder --no-sizes

# Specify output directory (defaults to temp directory)
./bin/foldyy /path/to/folder --output-dir /tmp

# Specify exact output file path
./bin/foldyy /path/to/folder --output tree.html

# Output only the file path (porcelain mode)
./bin/foldyy /path/to/folder --output-dir /tmp --porcelain
```

## Examples

```bash
# Generate HTML tree of current directory (saved to temp directory)
./bin/foldyy .

# Generate HTML with depth limit of 3
./bin/foldyy /var/www --max-depth 3

# Generate HTML without sizes
./bin/foldyy /path/to/folder --no-sizes

# Save HTML to specific directory
./bin/foldyy /path/to/folder --output-dir /tmp

# Save HTML to specific file
./bin/foldyy /path/to/folder --output folder-tree.html

# Get only the file path (useful for scripting)
./bin/foldyy /path/to/folder --output-dir /tmp --porcelain
```

## Features

- **HTML Tree View**: Generate interactive HTML with collapsible folders
- **Size Information**: Show file and folder sizes in human-readable format
- **Depth Control**: Limit the maximum depth of folder scanning
- **Collapsible Folders**: Click folders to expand/collapse
- **Flexible Output**: Save to temp directory, custom directory, or specific file
- **Porcelain Mode**: Output only file path for easy parsing in scripts

## Development

### Code Quality

The project uses PHPCS and PHPCBF for code quality:

```bash
# Check code style
composer lint

# Fix code style issues
composer format

# Run tests
composer test
```

### Testing

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

#### Test Coverage

The test suite includes:

- **CoreUtilsTest**: Tests for size formatting utilities
  - Formatting bytes, KB, MB, GB, TB
  - Handling edge cases (negative values, very large values)

- **FileUtilsTest**: Tests for file and folder operations
  - Getting folder tree structure
  - Calculating folder sizes
  - Handling empty folders, nested structures
  - Error handling for invalid paths

- **FoldyyServiceTest**: Tests for the main service class
  - HTML tree generation
  - Size display options
  - Max depth functionality

## License

MIT

## Author

Nilambar Sharma - nilambar@outlook.com
