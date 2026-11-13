<?php
/**
 * Module track taxonomy.
 *
 * A module's "track" answers: is this on a path toward WordPress Core, or is
 * it a practical tool we don't expect Core to ship? Orthogonal to the
 * Feature/Experiment bucket — either bucket can live on either track.
 *
 * @package AccessibilityLab
 */

declare( strict_types = 1 );

namespace AccessibilityLab;

/**
 * Class Track.
 *
 * Represents the track taxonomy for modules.
 */
final class Track {
	public const CORE_TRACK = 'core-track';
	public const PRACTICAL  = 'practical';

	/**
	 * Determines if a given value is a valid track.
	 *
	 * @param string $value The value to check.
	 * @return bool True if the value is a valid track, false otherwise.
	 */
	public static function is_valid( string $value ): bool {
		return self::CORE_TRACK === $value || self::PRACTICAL === $value;
	}
}
