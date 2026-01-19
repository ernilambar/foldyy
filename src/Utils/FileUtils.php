<?php

/**
 * FileUtils
 *
 * @package Foldyy
 */

namespace Nilambar\Foldyy\Utils;

use Exception;
use RuntimeException;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;

/**
 * FileUtils Class.
 *
 * Utility class for file and folder operations.
 *
 * @since 1.0.0
 */
class FileUtils
{
	/**
	 * Returns folder tree structure recursively.
	 *
	 * @since 1.0.0
	 *
	 * @param string $folder_path Folder path.
	 * @param int    $max_depth   Maximum depth to scan (default: 10).
	 * @return array Folder tree structure.
	 *
	 * @throws RuntimeException Error if folder does not exist.
	 */
	public static function getFolderTree(string $folder_path, int $max_depth = 10): array
	{
		$filesystem = new Filesystem();
		$finder     = new Finder();

		if (! $filesystem->exists($folder_path)) {
			throw new RuntimeException('Folder does not exist: ' . $folder_path);
		}

		if (! is_readable($folder_path)) {
			throw new RuntimeException('Folder is not readable: ' . $folder_path);
		}

		if (! is_dir($folder_path)) {
			throw new RuntimeException('Path is not a directory: ' . $folder_path);
		}

		return self::buildTree($folder_path, $folder_path, $max_depth, 0);
	}

	/**
	 * Build tree structure recursively.
	 *
	 * @since 1.0.0
	 *
	 * @param string $base_path     Base folder path.
	 * @param string $folder_path   Current folder path.
	 * @param int    $max_depth     Maximum depth.
	 * @param int    $current_depth Current depth.
	 * @return array Tree structure.
	 */
	private static function buildTree(string $base_path, string $folder_path, int $max_depth, int $current_depth): array
	{
		if ($current_depth >= $max_depth) {
			return [];
		}

		$finder = new Finder();
		$items  = [];

		try {
			$finder->in($folder_path)->depth(0)->sortByName();
		} catch (Exception $e) {
			// Skip directories that can't be scanned.
			return [];
		}

		foreach ($finder as $item) {
			try {
				$name = $item->getRelativePathname();
				$path = $item->getRealPath();

				// Skip if we can't get the real path (broken symlinks, etc.)
				if (false === $path) {
					continue;
				}

				if ($item->isDir()) {
					$size     = self::getFolderSize($path);
					$children = self::buildTree($base_path, $path, $max_depth, $current_depth + 1);

					$relative_path = $path;
					if (strpos($path, $base_path) === 0) {
						$relative_path = substr($path, strlen($base_path) + 1);
					}

					$items[] = [
						'type'           => 'folder',
						'name'           => $name,
						'path'           => $path,
						'relative_path'  => $relative_path,
						'size'           => $size,
						'size_formatted' => CoreUtils::getFormattedSize($size),
						'children'       => $children,
					];
				} else {
					$relative_path = $path;
					if (strpos($path, $base_path) === 0) {
						$relative_path = substr($path, strlen($base_path) + 1);
					}

					$items[] = [
						'type'           => 'file',
						'name'           => $name,
						'path'           => $path,
						'relative_path'  => $relative_path,
						'size'           => $item->getSize(),
						'size_formatted' => CoreUtils::getFormattedSize($item->getSize()),
						'children'       => null,
					];
				}
			} catch (Exception $e) {
				// Skip items that can't be processed (permission issues, etc.)
				continue;
			}
		}

		return $items;
	}

	/**
	 * Returns folder size.
	 *
	 * @since 1.0.0
	 *
	 * @param string $path Folder path.
	 * @return int Folder size in bytes.
	 */
	public static function getFolderSize(string $path): int
	{
		$size   = 0;
		$finder = new Finder();

		try {
			$finder->files()->in($path);

			foreach ($finder as $file) {
				$size += $file->getSize();
			}
		} catch (Exception $e) {
			// Return 0 if we can't calculate size.
			return 0;
		}

		return $size;
	}
}
