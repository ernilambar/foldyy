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
# Basic usage (text output)
./bin/foldyy /path/to/folder

# With maximum depth limit
./bin/foldyy /path/to/folder --max-depth 5

# Without size information
./bin/foldyy /path/to/folder --no-sizes

# Generate HTML output with collapsible folders
./bin/foldyy /path/to/folder --html

# Generate HTML and save to file
./bin/foldyy /path/to/folder --html --output tree.html
```

## Examples

```bash
# Display tree of current directory
./bin/foldyy .

# Display tree with depth limit of 3
./bin/foldyy /var/www --max-depth 3

# Display tree without sizes
./bin/foldyy /path/to/folder --no-sizes

# Generate HTML with collapsible folders
./bin/foldyy /path/to/folder --html --output folder-tree.html
```

## Features

- **Text Tree View**: Display folder structure in terminal with ASCII tree format
- **HTML Tree View**: Generate interactive HTML with collapsible folders (like the original yantra project)
- **Size Information**: Show file and folder sizes in human-readable format
- **Depth Control**: Limit the maximum depth of folder scanning
- **Collapsible Folders**: In HTML mode, click folders to expand/collapse (just like the source project)

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
  - Text tree generation
  - HTML tree generation
  - Size display options
  - Max depth functionality

## License

MIT

## Author

Nilambar Sharma - nilambar@outlook.com
