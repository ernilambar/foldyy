<?php

/**
 * Folder Tree HTML Template
 *
 * @package Foldyy
 *
 * Available variables:
 * - $folder_path: Full path to the folder
 * - $tree: Tree structure array
 * - $total_size: Total size in bytes
 * - $total_size_formatted: Formatted total size string
 * - $show_sizes: Whether to show sizes
 */

?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Folder Tree - <?php echo htmlspecialchars(basename($folder_path), ENT_QUOTES, 'UTF-8'); ?></title>
	<style>
		/* Folder Tree Viewer Styles */
		* {
			box-sizing: border-box;
			margin: 0;
			padding: 0;
		}

		body {
			font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
			line-height: 1.6;
			color: #333;
			background-color: #f5f5f5;
		}

		.container {
			max-width: 1200px;
			margin: 0 auto;
			padding: 20px;
			background-color: #fff;
			min-height: 100vh;
			box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
		}

		/* Header */
		.header {
			border-bottom: 2px solid #e0e0e0;
			padding-bottom: 15px;
			margin-bottom: 20px;
		}

		.header h1 {
			font-size: 24px;
			font-weight: 600;
			color: #2c3e50;
			margin-bottom: 10px;
			word-break: break-all;
		}

		.header-info {
			display: flex;
			align-items: center;
			gap: 15px;
		}

		.total-size {
			font-size: 14px;
			color: #666;
		}

		.total-size strong {
			color: #2c3e50;
			font-weight: 600;
		}

		.tree-controls {
			display: flex;
			gap: 8px;
			align-items: center;
		}

		.tree-control-btn {
			background: #f0f0f0;
			border: 1px solid #d0d0d0;
			border-radius: 4px;
			padding: 4px 10px;
			font-size: 12px;
			color: #666;
			cursor: pointer;
			transition: all 0.2s;
			font-family: inherit;
		}

		.tree-control-btn:hover {
			background: #e0e0e0;
			border-color: #b0b0b0;
			color: #333;
		}

		.tree-control-btn:active {
			background: #d0d0d0;
		}

		/* Main Content */
		.main {
			margin-bottom: 30px;
		}

		.empty-folder {
			text-align: center;
			padding: 40px;
			color: #999;
		}

		/* Tree Structure */
		.tree {
			font-family: 'Monaco', 'Menlo', 'Ubuntu Mono', monospace;
			font-size: 14px;
		}

		.tree-item {
			margin: 2px 0;
		}

		.tree-item-content {
			display: flex;
			align-items: center;
			padding: 4px 8px;
			border-radius: 4px;
			transition: background-color 0.2s;
		}

		.tree-item-content:hover {
			background-color: #f0f0f0;
		}

		.tree-item.folder .tree-item-content {
			cursor: pointer;
			font-weight: 500;
		}

		.tree-item.file .tree-item-content {
			cursor: default;
		}

		.tree-toggle {
			background: none;
			border: none;
			cursor: pointer;
			padding: 2px 4px;
			margin-right: 4px;
			display: inline-flex;
			align-items: center;
			justify-content: center;
			width: 20px;
			height: 20px;
			transition: transform 0.2s;
		}

		.tree-toggle:hover {
			background-color: #e0e0e0;
			border-radius: 3px;
		}

		.tree-toggle.expanded .toggle-icon {
			transform: rotate(90deg);
		}

		.toggle-icon {
			display: inline-block;
			font-size: 10px;
			color: #666;
			transition: transform 0.2s;
		}

		.tree-toggle-placeholder {
			width: 20px;
			display: inline-block;
			margin-right: 4px;
		}

		.tree-icon {
			margin-right: 6px;
			font-size: 16px;
			display: inline-block;
			width: 20px;
			text-align: center;
		}

		.tree-size {
			color: #666;
			font-size: 12px;
			white-space: nowrap;
			margin-right: 10px;
			width: 80px;
			display: inline-block;
			text-align: right;
		}

		.tree-name {
			flex: 1;
			color: #2c3e50;
			margin-right: 10px;
			word-break: break-all;
		}

		.tree-item.folder .tree-name {
			font-weight: 500;
		}

		.tree-children {
			margin-left: 24px;
			border-left: 1px solid #e0e0e0;
			padding-left: 8px;
		}

		.tree-item[data-level="0"] .tree-children {
			border-left: none;
			padding-left: 0;
		}

		/* Responsive */
		@media (max-width: 768px) {
			.container {
				padding: 15px;
			}

			.header h1 {
				font-size: 20px;
			}

			.header-info {
				flex-wrap: wrap;
				gap: 10px;
			}

			.tree {
				font-size: 13px;
			}

			.tree-children {
				margin-left: 16px;
			}
		}
	</style>
</head>
<body>
	<div class="container">
		<div class="header">
			<h1>📁 <?php echo htmlspecialchars($folder_path, ENT_QUOTES, 'UTF-8'); ?></h1>
			<div class="header-info">
				<?php if ($show_sizes) : ?>
				<span class="total-size">Total Size: <strong><?php echo htmlspecialchars($total_size_formatted, ENT_QUOTES, 'UTF-8'); ?></strong></span>
				<?php endif; ?>
				<div class="tree-controls">
					<button type="button" class="tree-control-btn" id="expand-all-btn">Expand All</button>
					<button type="button" class="tree-control-btn" id="collapse-all-btn">Collapse All</button>
				</div>
			</div>
		</div>
		<div class="main">
			<?php if (empty($tree)) : ?>
				<div class="empty-folder">
					<p>This folder is empty.</p>
				</div>
			<?php else : ?>
				<div class="tree">
					<?php
					if (! function_exists('render_tree_node')) {
						/**
						 * Render tree node recursively.
						 *
						 * @param array $items Tree items.
						 * @param int   $level Current depth level.
						 * @param bool  $show_sizes Whether to show sizes.
						 */
						function render_tree_node(array $items, int $level = 0, bool $show_sizes = true): void
						{
							foreach ($items as $item) {
								$icon         = 'folder' === $item['type'] ? '📁' : '📄';
								$has_children = 'folder' === $item['type'] && ! empty($item['children']);
								$item_id      = 'item-' . md5($item['path']);

								if ('folder' === $item['type']) {
									?>
									<div class="tree-item folder" data-level="<?php echo htmlspecialchars((string) $level, ENT_QUOTES, 'UTF-8'); ?>" data-path="<?php echo htmlspecialchars($item['path'], ENT_QUOTES, 'UTF-8'); ?>">
										<div class="tree-item-content">
											<?php if ($show_sizes) : ?>
												<span class="tree-size"><?php echo htmlspecialchars($item['size_formatted'], ENT_QUOTES, 'UTF-8'); ?></span>
											<?php endif; ?>
											<?php if ($has_children) : ?>
												<button class="tree-toggle" aria-label="Toggle folder" data-target="<?php echo htmlspecialchars($item_id, ENT_QUOTES, 'UTF-8'); ?>">
													<span class="toggle-icon">▶</span>
												</button>
											<?php else : ?>
												<span class="tree-toggle-placeholder"></span>
											<?php endif; ?>
											<span class="tree-icon"><?php echo $icon; ?></span>
											<span class="tree-name"><?php echo htmlspecialchars($item['name'], ENT_QUOTES, 'UTF-8'); ?></span>
										</div>
										<?php if ($has_children) : ?>
											<div class="tree-children" id="<?php echo htmlspecialchars($item_id, ENT_QUOTES, 'UTF-8'); ?>" style="display: none;">
												<?php render_tree_node($item['children'], $level + 1, $show_sizes); ?>
											</div>
										<?php endif; ?>
									</div>
									<?php
								} else {
									?>
									<div class="tree-item file" data-level="<?php echo htmlspecialchars((string) $level, ENT_QUOTES, 'UTF-8'); ?>" data-path="<?php echo htmlspecialchars($item['path'], ENT_QUOTES, 'UTF-8'); ?>">
										<div class="tree-item-content">
											<?php if ($show_sizes) : ?>
												<span class="tree-size"><?php echo htmlspecialchars($item['size_formatted'], ENT_QUOTES, 'UTF-8'); ?></span>
											<?php endif; ?>
											<span class="tree-toggle-placeholder"></span>
											<span class="tree-icon"><?php echo $icon; ?></span>
											<span class="tree-name"><?php echo htmlspecialchars($item['name'], ENT_QUOTES, 'UTF-8'); ?></span>
										</div>
									</div>
									<?php
								}
							}
						}
					}
					render_tree_node($tree, 0, $show_sizes);
					?>
				</div>
			<?php endif; ?>
		</div>
	</div>
	<script>
		/**
		 * Folder Tree Viewer JavaScript
		 * Handles expand/collapse functionality for folder tree
		 */
		(function() {
			'use strict';

			// Wait for DOM to be ready
			document.addEventListener('DOMContentLoaded', function() {
				initTreeViewer();
			});

			/**
			 * Initialize tree viewer functionality
			 */
			function initTreeViewer() {
				const toggleButtons = document.querySelectorAll('.tree-toggle');

				toggleButtons.forEach(function(button) {
					button.addEventListener('click', function(e) {
						e.stopPropagation();
						toggleFolder(this);
					});
				});

				// Also allow clicking on folder name to toggle
				const folderItems = document.querySelectorAll('.tree-item.folder .tree-item-content');

				folderItems.forEach(function(item) {
					item.addEventListener('click', function(e) {
						// Only toggle if clicking on the content area, not on the toggle button
						if (e.target.classList.contains('tree-toggle')) {
							return;
						}

						const toggleBtn = this.querySelector('.tree-toggle');
						if (toggleBtn) {
							toggleFolder(toggleBtn);
						}
					});
				});

				// Expand all button
				const expandAllBtn = document.getElementById('expand-all-btn');
				if (expandAllBtn) {
					expandAllBtn.addEventListener('click', function() {
						expandAllFolders();
					});
				}

				// Collapse all button
				const collapseAllBtn = document.getElementById('collapse-all-btn');
				if (collapseAllBtn) {
					collapseAllBtn.addEventListener('click', function() {
						collapseAllFolders();
					});
				}
			}

			/**
			 * Toggle folder expand/collapse
			 *
			 * @param {HTMLElement} button Toggle button element
			 */
			function toggleFolder(button) {
				const targetId = button.getAttribute('data-target');
				const children = document.getElementById(targetId);

				if (!children) {
					return;
				}

				const isExpanded = children.style.display !== 'none';

				if (isExpanded) {
					// Collapse
					children.style.display = 'none';
					button.classList.remove('expanded');
				} else {
					// Expand
					children.style.display = 'block';
					button.classList.add('expanded');
				}
			}

			/**
			 * Expand all folders
			 */
			function expandAllFolders() {
				const allChildren = document.querySelectorAll('.tree-children');
				const allToggleButtons = document.querySelectorAll('.tree-toggle');

				allChildren.forEach(function(children) {
					children.style.display = 'block';
				});

				allToggleButtons.forEach(function(button) {
					button.classList.add('expanded');
				});
			}

			/**
			 * Collapse all folders
			 */
			function collapseAllFolders() {
				const allChildren = document.querySelectorAll('.tree-children');
				const allToggleButtons = document.querySelectorAll('.tree-toggle');

				allChildren.forEach(function(children) {
					children.style.display = 'none';
				});

				allToggleButtons.forEach(function(button) {
					button.classList.remove('expanded');
				});
			}
		})();
	</script>
</body>
</html>
