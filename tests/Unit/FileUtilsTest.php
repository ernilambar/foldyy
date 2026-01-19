<?php

/**
 * FileUtilsTest
 *
 * @package Foldyy
 */

namespace Nilambar\Foldyy\Tests\Unit;

use Nilambar\Foldyy\Utils\FileUtils;
use PHPUnit\Framework\TestCase;
use RuntimeException;

/**
 * FileUtils Test Class.
 *
 * @since 1.0.0
 */
class FileUtilsTest extends TestCase
{
	/**
	 * Temporary test directory.
	 *
	 * @var string
	 */
	private $temp_dir;

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
	 * Test getFolderTree with empty folder.
	 *
	 * @since 1.0.0
	 */
	public function testGetFolderTreeEmpty()
	{
		$tree = FileUtils::getFolderTree($this->temp_dir);

		$this->assertIsArray($tree);
		$this->assertEmpty($tree);
	}

	/**
	 * Test getFolderTree with files and folders.
	 *
	 * @since 1.0.0
	 */
	public function testGetFolderTreeWithFiles()
	{
		// Create test structure.
		file_put_contents($this->temp_dir . DIRECTORY_SEPARATOR . 'file1.txt', 'content1');
		file_put_contents($this->temp_dir . DIRECTORY_SEPARATOR . 'file2.txt', 'content2');
		mkdir($this->temp_dir . DIRECTORY_SEPARATOR . 'subdir', 0755, true);
		file_put_contents($this->temp_dir . DIRECTORY_SEPARATOR . 'subdir' . DIRECTORY_SEPARATOR . 'file3.txt', 'content3');

		$tree = FileUtils::getFolderTree($this->temp_dir);

		$this->assertIsArray($tree);
		$this->assertNotEmpty($tree);

		// Check structure.
		$has_file1 = false;
		$has_file2 = false;
		$has_subdir = false;

		foreach ($tree as $item) {
			if ('file' === $item['type'] && 'file1.txt' === $item['name']) {
				$has_file1 = true;
				$this->assertArrayHasKey('size', $item);
				$this->assertArrayHasKey('size_formatted', $item);
			}
			if ('file' === $item['type'] && 'file2.txt' === $item['name']) {
				$has_file2 = true;
			}
			if ('folder' === $item['type'] && 'subdir' === $item['name']) {
				$has_subdir = true;
				$this->assertArrayHasKey('children', $item);
				$this->assertIsArray($item['children']);
				$this->assertNotEmpty($item['children']);
			}
		}

		$this->assertTrue($has_file1, 'file1.txt should be in tree');
		$this->assertTrue($has_file2, 'file2.txt should be in tree');
		$this->assertTrue($has_subdir, 'subdir should be in tree');
	}

	/**
	 * Test getFolderTree with max_depth limit.
	 *
	 * @since 1.0.0
	 */
	public function testGetFolderTreeMaxDepth()
	{
		// Create nested structure.
		mkdir($this->temp_dir . DIRECTORY_SEPARATOR . 'level1', 0755, true);
		mkdir($this->temp_dir . DIRECTORY_SEPARATOR . 'level1' . DIRECTORY_SEPARATOR . 'level2', 0755, true);
		mkdir($this->temp_dir . DIRECTORY_SEPARATOR . 'level1' . DIRECTORY_SEPARATOR . 'level2' . DIRECTORY_SEPARATOR . 'level3', 0755, true);
		file_put_contents($this->temp_dir . DIRECTORY_SEPARATOR . 'level1' . DIRECTORY_SEPARATOR . 'level2' . DIRECTORY_SEPARATOR . 'level3' . DIRECTORY_SEPARATOR . 'file.txt', 'content');

		// Test with depth 1.
		$tree = FileUtils::getFolderTree($this->temp_dir, 1);
		$has_level1 = false;
		foreach ($tree as $item) {
			if ('folder' === $item['type'] && 'level1' === $item['name']) {
				$has_level1 = true;
				// Should not have children due to depth limit.
				$this->assertEmpty($item['children']);
			}
		}
		$this->assertTrue($has_level1);
	}

	/**
	 * Test getFolderTree with non-existent folder.
	 *
	 * @since 1.0.0
	 */
	public function testGetFolderTreeNonExistent()
	{
		$this->expectException(RuntimeException::class);
		$this->expectExceptionMessage('Folder does not exist');

		FileUtils::getFolderTree('/non/existent/path');
	}

	/**
	 * Test getFolderTree with file instead of folder.
	 *
	 * @since 1.0.0
	 */
	public function testGetFolderTreeFileInsteadOfFolder()
	{
		$file_path = $this->temp_dir . DIRECTORY_SEPARATOR . 'test.txt';
		file_put_contents($file_path, 'content');

		$this->expectException(RuntimeException::class);
		$this->expectExceptionMessage('Path is not a directory');

		FileUtils::getFolderTree($file_path);
	}

	/**
	 * Test getFolderSize with empty folder.
	 *
	 * @since 1.0.0
	 */
	public function testGetFolderSizeEmpty()
	{
		$size = FileUtils::getFolderSize($this->temp_dir);

		$this->assertEquals(0, $size);
	}

	/**
	 * Test getFolderSize with files.
	 *
	 * @since 1.0.0
	 */
	public function testGetFolderSizeWithFiles()
	{
		// Create files with known sizes.
		file_put_contents($this->temp_dir . DIRECTORY_SEPARATOR . 'file1.txt', 'content1'); // 8 bytes.
		file_put_contents($this->temp_dir . DIRECTORY_SEPARATOR . 'file2.txt', 'content2'); // 8 bytes.
		mkdir($this->temp_dir . DIRECTORY_SEPARATOR . 'subdir', 0755, true);
		file_put_contents($this->temp_dir . DIRECTORY_SEPARATOR . 'subdir' . DIRECTORY_SEPARATOR . 'file3.txt', 'content3'); // 8 bytes.

		$size = FileUtils::getFolderSize($this->temp_dir);

		// Should be at least 24 bytes (3 files * 8 bytes each).
		$this->assertGreaterThanOrEqual(24, $size);
	}

	/**
	 * Test getFolderSize with nested folders.
	 *
	 * @since 1.0.0
	 */
	public function testGetFolderSizeNested()
	{
		// Create nested structure.
		mkdir($this->temp_dir . DIRECTORY_SEPARATOR . 'level1', 0755, true);
		mkdir($this->temp_dir . DIRECTORY_SEPARATOR . 'level1' . DIRECTORY_SEPARATOR . 'level2', 0755, true);
		file_put_contents($this->temp_dir . DIRECTORY_SEPARATOR . 'level1' . DIRECTORY_SEPARATOR . 'level2' . DIRECTORY_SEPARATOR . 'file.txt', 'test content'); // 12 bytes.

		$size = FileUtils::getFolderSize($this->temp_dir);

		// Should include nested file size.
		$this->assertGreaterThanOrEqual(12, $size);
	}
}
