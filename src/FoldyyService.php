<?php

/**
 * FoldyyService
 *
 * @package Foldyy
 */

namespace Nilambar\Foldyy;

use Nilambar\Foldyy\Utils\CoreUtils;
use Nilambar\Foldyy\Utils\FileUtils;
use RuntimeException;

/**
 * FoldyyService Class.
 *
 * Main service class for generating folder tree output.
 *
 * @since 1.0.0
 */
class FoldyyService
{
	/**
	 * Generate tree output for a folder.
	 *
	 * @since 1.0.0
	 *
	 * @param string $folder_path Folder path.
	 * @param int    $max_depth Maximum depth.
	 * @param bool   $show_sizes Whether to show sizes.
	 * @return string Tree output.
	 *
	 * @throws RuntimeException Error if folder does not exist.
	 */
	public function generateTree(string $folder_path, int $max_depth = 10, bool $show_sizes = true): string
	{
		$tree = FileUtils::getFolderTree($folder_path, $max_depth);
		$total_size = FileUtils::getFolderSize($folder_path);

		$output = [];
		$output[] = '📁 ' . $folder_path;

		if ($show_sizes) {
			$output[] = 'Total Size: ' . CoreUtils::getFormattedSize($total_size);
		}

		$output[] = '';

		if (empty($tree)) {
			$output[] = '(empty folder)';
		} else {
			$output[] = $this->formatTree($tree, $show_sizes);
		}

		return implode("\n", $output);
	}

	/**
	 * Generate HTML tree output for a folder.
	 *
	 * @since 1.0.0
	 *
	 * @param string $folder_path Folder path.
	 * @param int    $max_depth Maximum depth.
	 * @param bool   $show_sizes Whether to show sizes.
	 * @return string HTML output.
	 *
	 * @throws RuntimeException Error if folder does not exist.
	 */
	public function generateHtmlTree(string $folder_path, int $max_depth = 10, bool $show_sizes = true): string
	{
		$tree = FileUtils::getFolderTree($folder_path, $max_depth);
		$total_size = FileUtils::getFolderSize($folder_path);
		$total_size_formatted = CoreUtils::getFormattedSize($total_size);

		// Template variables.
		$template_vars = [
			'folder_path'          => $folder_path,
			'tree'                 => $tree,
			'total_size'           => $total_size,
			'total_size_formatted' => $total_size_formatted,
			'show_sizes'           => $show_sizes,
		];

		return $this->renderTemplate('tree.php', $template_vars);
	}

	/**
	 * Render a template file with variables.
	 *
	 * @since 1.0.0
	 *
	 * @param string $template_name Template file name.
	 * @param array  $vars Variables to pass to template.
	 * @return string Rendered template output.
	 *
	 * @throws RuntimeException If template file not found.
	 */
	private function renderTemplate(string $template_name, array $vars = []): string
	{
		$template_path = __DIR__ . '/../templates/' . $template_name;

		if (! file_exists($template_path)) {
			throw new RuntimeException('Template not found: ' . $template_name);
		}

		// Extract variables for template.
		extract($vars, EXTR_SKIP);

		ob_start();
		include $template_path;
		return ob_get_clean();
	}

	/**
	 * Format tree structure for output.
	 *
	 * @since 1.0.0
	 *
	 * @param array $tree Tree structure.
	 * @param bool  $show_sizes Whether to show sizes.
	 * @param int   $level Current level.
	 * @param array $prefixes Prefixes for tree lines.
	 * @return string Formatted tree.
	 */
	private function formatTree(array $tree, bool $show_sizes = true, int $level = 0, array $prefixes = []): string
	{
		$output = [];
		$count = count($tree);

		foreach ($tree as $index => $item) {
			$is_last = ( $index === $count - 1 );
			$icon = ( 'folder' === $item['type'] ) ? '📁' : '📄';

			// Build prefix for this line.
			$line_prefix = '';
			if ($level > 0) {
				$line_prefix = implode('', $prefixes);
				$line_prefix .= $is_last ? '└── ' : '├── ';
			}

			// Build name with size.
			$name = $icon . ' ' . $item['name'];
			if ($show_sizes) {
				$name .= ' (' . $item['size_formatted'] . ')';
			}

			$output[] = $line_prefix . $name;

			// Recursively format children.
			if ('folder' === $item['type'] && ! empty($item['children'])) {
				$child_prefixes = $prefixes;
				$child_prefixes[] = $is_last ? '    ' : '│   ';
				$output[] = $this->formatTree($item['children'], $show_sizes, $level + 1, $child_prefixes);
			}
		}

		return implode("\n", $output);
	}
}
