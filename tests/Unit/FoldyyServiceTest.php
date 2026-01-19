<?php

/**
 * FoldyyServiceTest
 *
 * @package Foldyy
 */

namespace Nilambar\Foldyy\Tests\Unit;

use Nilambar\Foldyy\FoldyyService;
use PHPUnit\Framework\TestCase;
use RuntimeException;

/**
 * FoldyyService Test Class.
 *
 * @since 1.0.0
 */
class FoldyyServiceTest extends TestCase
{
	/**
	 * Temporary test directory.
	 *
	 * @var string
	 */
	private $temp_dir;

	/**
	 * FoldyyService instance.
	 *
	 * @var FoldyyService
	 */
	private $service;

	/**
	 * Set up test environment.
	 *
	 * @since 1.0.0
	 */
	protected function setUp(): void
	{
		parent::setUp();
		$this->temp_dir = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'foldyy_test_' . uniqid();
		mkdir($this->temp_dir, 0755, true);
		$this->service = new FoldyyService();
	}

	/**
	 * Tear down test environment.
	 *
	 * @since 1.0.0
	 */
	protected function tearDown(): void
	{
		$this->cleanupDirectory($this->temp_dir);
		parent::tearDown();
	}

	/**
	 * Recursively delete directory.
	 *
	 * @since 1.0.0
	 *
	 * @param string $dir Directory path.
	 */
	private function cleanupDirectory(string $dir): void
	{
		if (! is_dir($dir)) {
			return;
		}

		$files = array_diff(scandir($dir), [ '.', '..' ]);

		foreach ($files as $file) {
			$path = $dir . DIRECTORY_SEPARATOR . $file;
			if (is_dir($path)) {
				$this->cleanupDirectory($path);
			} else {
				unlink($path);
			}
		}

		rmdir($dir);
	}

	/**
	 * Test generateTree with empty folder.
	 *
	 * @since 1.0.0
	 */
	public function testGenerateTreeEmpty()
	{
		$output = $this->service->generateTree($this->temp_dir);

		$this->assertIsString($output);
		$this->assertStringContainsString($this->temp_dir, $output);
		$this->assertStringContainsString('(empty folder)', $output);
	}

	/**
	 * Test generateTree with files and folders.
	 *
	 * @since 1.0.0
	 */
	public function testGenerateTreeWithContent()
	{
		// Create test structure.
		file_put_contents($this->temp_dir . DIRECTORY_SEPARATOR . 'file1.txt', 'content1');
		mkdir($this->temp_dir . DIRECTORY_SEPARATOR . 'subdir', 0755, true);
		file_put_contents($this->temp_dir . DIRECTORY_SEPARATOR . 'subdir' . DIRECTORY_SEPARATOR . 'file2.txt', 'content2');

		$output = $this->service->generateTree($this->temp_dir);

		$this->assertIsString($output);
		$this->assertStringContainsString($this->temp_dir, $output);
		$this->assertStringContainsString('file1.txt', $output);
		$this->assertStringContainsString('subdir', $output);
		$this->assertStringContainsString('Total Size:', $output);
	}

	/**
	 * Test generateTree without sizes.
	 *
	 * @since 1.0.0
	 */
	public function testGenerateTreeNoSizes()
	{
		file_put_contents($this->temp_dir . DIRECTORY_SEPARATOR . 'file1.txt', 'content1');

		$output = $this->service->generateTree($this->temp_dir, 10, false);

		$this->assertIsString($output);
		$this->assertStringNotContainsString('Total Size:', $output);
	}

	/**
	 * Test generateTree with non-existent folder.
	 *
	 * @since 1.0.0
	 */
	public function testGenerateTreeNonExistent()
	{
		$this->expectException(RuntimeException::class);

		$this->service->generateTree('/non/existent/path');
	}

	/**
	 * Test generateHtmlTree with empty folder.
	 *
	 * @since 1.0.0
	 */
	public function testGenerateHtmlTreeEmpty()
	{
		$output = $this->service->generateHtmlTree($this->temp_dir);

		$this->assertIsString($output);
		$this->assertStringContainsString('<!DOCTYPE html>', $output);
		$this->assertStringContainsString('<html', $output);
		$this->assertStringContainsString($this->temp_dir, $output);
		$this->assertStringContainsString('This folder is empty', $output);
	}

	/**
	 * Test generateHtmlTree with files and folders.
	 *
	 * @since 1.0.0
	 */
	public function testGenerateHtmlTreeWithContent()
	{
		// Create test structure.
		file_put_contents($this->temp_dir . DIRECTORY_SEPARATOR . 'file1.txt', 'content1');
		mkdir($this->temp_dir . DIRECTORY_SEPARATOR . 'subdir', 0755, true);
		file_put_contents($this->temp_dir . DIRECTORY_SEPARATOR . 'subdir' . DIRECTORY_SEPARATOR . 'file2.txt', 'content2');

		$output = $this->service->generateHtmlTree($this->temp_dir);

		$this->assertIsString($output);
		$this->assertStringContainsString('<!DOCTYPE html>', $output);
		$this->assertStringContainsString('<style>', $output);
		$this->assertStringContainsString('<script>', $output);
		$this->assertStringContainsString('file1.txt', $output);
		$this->assertStringContainsString('subdir', $output);
		$this->assertStringContainsString('tree-toggle', $output);
		$this->assertStringContainsString('tree-children', $output);
	}

	/**
	 * Test generateHtmlTree without sizes.
	 *
	 * @since 1.0.0
	 */
	public function testGenerateHtmlTreeNoSizes()
	{
		file_put_contents($this->temp_dir . DIRECTORY_SEPARATOR . 'file1.txt', 'content1');

		$output = $this->service->generateHtmlTree($this->temp_dir, 10, false);

		$this->assertIsString($output);
		// Should still contain HTML structure.
		$this->assertStringContainsString('<!DOCTYPE html>', $output);
	}

	/**
	 * Test generateHtmlTree with max_depth.
	 *
	 * @since 1.0.0
	 */
	public function testGenerateHtmlTreeMaxDepth()
	{
		// Create nested structure.
		mkdir($this->temp_dir . DIRECTORY_SEPARATOR . 'level1', 0755, true);
		mkdir($this->temp_dir . DIRECTORY_SEPARATOR . 'level1' . DIRECTORY_SEPARATOR . 'level2', 0755, true);
		file_put_contents($this->temp_dir . DIRECTORY_SEPARATOR . 'level1' . DIRECTORY_SEPARATOR . 'level2' . DIRECTORY_SEPARATOR . 'file.txt', 'content');

		$output = $this->service->generateHtmlTree($this->temp_dir, 1);

		$this->assertIsString($output);
		$this->assertStringContainsString('level1', $output);
	}

	/**
	 * Test generateHtmlTree with non-existent folder.
	 *
	 * @since 1.0.0
	 */
	public function testGenerateHtmlTreeNonExistent()
	{
		$this->expectException(RuntimeException::class);

		$this->service->generateHtmlTree('/non/existent/path');
	}
}
