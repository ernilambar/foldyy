<?php

/**
 * CoreUtilsTest
 *
 * @package Foldyy
 */

namespace Nilambar\Foldyy\Tests\Unit;

use Nilambar\Foldyy\Utils\CoreUtils;
use PHPUnit\Framework\TestCase;

/**
 * CoreUtils Test Class.
 *
 * @since 1.0.0
 */
class CoreUtilsTest extends TestCase
{
	/**
	 * Test getFormattedSize method with various sizes.
	 *
	 * @since 1.0.0
	 */
	public function testGetFormattedSize()
	{
		// Test bytes.
		$this->assertEquals('0 B', CoreUtils::getFormattedSize(0));
		$this->assertEquals('500 B', CoreUtils::getFormattedSize(500));
		$this->assertEquals('1023 B', CoreUtils::getFormattedSize(1023));

		// Test kilobytes.
		$this->assertEquals('1 KB', CoreUtils::getFormattedSize(1024));
		$this->assertEquals('1.5 KB', CoreUtils::getFormattedSize(1536));
		$this->assertEquals('1024 KB', CoreUtils::getFormattedSize(1048575));

		// Test megabytes.
		$this->assertEquals('1 MB', CoreUtils::getFormattedSize(1048576));
		$this->assertEquals('1.5 MB', CoreUtils::getFormattedSize(1572864));
		// 1073741824 bytes = 1 GB (1024^3), not 1024 MB.
		$this->assertEquals('1 GB', CoreUtils::getFormattedSize(1073741824));

		// Test gigabytes.
		$this->assertEquals('1 GB', CoreUtils::getFormattedSize(1073741824));
		$this->assertEquals('2.5 GB', CoreUtils::getFormattedSize(2684354560));

		// Test terabytes.
		$this->assertEquals('1 TB', CoreUtils::getFormattedSize(1099511627776));
		$this->assertEquals('5.5 TB', CoreUtils::getFormattedSize(6047313952768));
	}

	/**
	 * Test getFormattedSize with negative values.
	 *
	 * @since 1.0.0
	 */
	public function testGetFormattedSizeNegative()
	{
		// Negative values should be treated as 0.
		$this->assertEquals('0 B', CoreUtils::getFormattedSize(-100));
		$this->assertEquals('0 B', CoreUtils::getFormattedSize(-1024));
	}

	/**
	 * Test getFormattedSize with very large values.
	 *
	 * @since 1.0.0
	 */
	public function testGetFormattedSizeLarge()
	{
		// Very large values should cap at TB.
		$very_large = 1099511627776 * 1000; // 1000 TB.
		$result = CoreUtils::getFormattedSize($very_large);
		$this->assertStringContainsString('TB', $result);
	}
}
