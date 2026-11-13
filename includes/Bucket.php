<?php
/**
 * Module bucket taxonomy.
 *
 * @package AccessibilityLab
 */

declare( strict_types = 1 );

namespace AccessibilityLab;

/**
 * Class Bucket.
 *
 * Represents the bucket taxonomy for modules.
 */
final class Bucket {
	public const FEATURE    = 'feature';
	public const EXPERIMENT = 'experiment';

	/**
	 * Determines if a given value is a valid bucket.
	 *
	 * @param string $value The value to check.
	 * @return bool True if the value is a valid bucket, false otherwise.
	 */
	public static function is_valid( string $value ): bool {
		return self::FEATURE === $value || self::EXPERIMENT === $value;
	}
}
