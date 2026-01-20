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

# Specify output directory (defaults to temp directory)
./bin/foldyy /path/to/folder --output-dir /tmp

# Specify exact output file path
./bin/foldyy /path/to/folder --output tree.html

# Output only the file path (porcelain mode, useful for scripting)
./bin/foldyy /path/to/folder --output-dir /tmp --porcelain
```

## Features

- **HTML Tree View**: Generate interactive HTML with collapsible folders
- **Size Information**: Show file and folder sizes in human-readable format
- **Depth Control**: Limit the maximum depth of folder scanning
- **Collapsible Folders**: Click folders to expand/collapse
- **Flexible Output**: Save to temp directory, custom directory, or specific file
- **Porcelain Mode**: Output only file path for easy parsing in scripts

## License

MIT

## Author

Nilambar Sharma - nilambar@outlook.com
