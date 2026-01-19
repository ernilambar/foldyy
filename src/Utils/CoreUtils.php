<?php

/**
 * CoreUtils
 *
 * @package Foldyy
 */

namespace Nilambar\Foldyy\Utils;

/**
 * CoreUtils Class.
 *
 * Core utility functions.
 *
 * @since 1.0.0
 */
class CoreUtils
{
	/**
	 * Return formatted size string.
	 *
	 * @since 1.0.0
	 *
	 * @param int $bytes Size.
	 * @return string Formatted size string.
	 */
	public static function getFormattedSize(int $bytes): string
	{
		$units = [ 'B', 'KB', 'MB', 'GB', 'TB' ];

		$bytes = max($bytes, 0);
		$pow   = floor(( $bytes ? log($bytes) : 0 ) / log(1024));
		$pow   = min($pow, count($units) - 1);

		$bytes /= ( 1 << ( 10 * $pow ) );

		return round($bytes, 2) . ' ' . $units[ $pow ];
	}
}
