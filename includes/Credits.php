<?php
/**
 * Attribution block for adopted Features.
 *
 * @package AccessibilityLab
 */

declare( strict_types = 1 );

namespace AccessibilityLab;

/**
 * Class Credits.
 *
 * Represents the credits for adopted features.
 */
final class Credits {

	/**
	 * Class Credits.
	 *
	 * Represents the credits for adopted features.
	 *
	 * @param string      $author The author of the adopted feature.
	 * @param string|null $source_plugin_slug The slug of the source plugin, if applicable.
	 * @param string|null $source_plugin_url The URL of the source plugin, if applicable.
	 * @param string|null $license The license of the adopted feature, if applicable.
	 */
	public function __construct(
		public readonly string $author,
		public readonly ?string $source_plugin_slug = null,
		public readonly ?string $source_plugin_url = null,
		public readonly ?string $license = null
	) {}

	/**
	 * Converts the credits object to an associative array.
	 *
	 * @return array<string, string|null> The credits as an array.
	 */
	public function to_array(): array {
		return array(
			'author'             => $this->author,
			'source_plugin_slug' => $this->source_plugin_slug,
			'source_plugin_url'  => $this->source_plugin_url,
			'license'            => $this->license,
		);
	}
}
