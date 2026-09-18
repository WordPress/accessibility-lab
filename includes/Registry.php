<?php
/**
 * Module registry.
 *
 * @package AccessibilityLab
 */

declare( strict_types = 1 );

namespace AccessibilityLab;

use AccessibilityLab\Abstracts\Abstract_Module;

/**
 * Class Registry.
 *
 * Manages the registration and settings of modules.
 */
final class Registry {

	public const OPTION_KEY = 'accessibility_lab_settings';

	/**
	 * The registered modules.
	 *
	 * @var array<string, Abstract_Module>
	 */
	private array $modules = array();

	/**
	 * Registers a module.
	 *
	 * @param Abstract_Module $module The module to register.
	 */
	public function register( Abstract_Module $module ): void {
		$id = $module->id();
		if ( isset( $this->modules[ $id ] ) ) {
			return;
		}
		if ( ! Bucket::is_valid( $module->bucket() ) ) {
			return;
		}
		if ( ! Track::is_valid( $module->track() ) ) {
			return;
		}
		$this->modules[ $id ] = $module;
	}

	/**
	 * Retrieves all registered modules.
	 *
	 * @return array<string, Abstract_Module>
	 */
	public function all(): array {
		return $this->modules;
	}

	/**
	 * Retrieves modules filtered by their bucket.
	 *
	 * @param string $bucket Bucket::FEATURE|EXPERIMENT.
	 * @return array<string, Abstract_Module>
	 */
	public function by_bucket( string $bucket ): array {
		return array_filter(
			$this->modules,
			static fn( Abstract_Module $m ): bool => $m->bucket() === $bucket
		);
	}

	/**
	 * Retrieves modules filtered by their track.
	 *
	 * @param string $track Track::CORE_TRACK|PRACTICAL.
	 * @return array<string, Abstract_Module>
	 */
	public function by_track( string $track ): array {
		return array_filter(
			$this->modules,
			static fn( Abstract_Module $m ): bool => $m->track() === $track
		);
	}

	/**
	 * Retrieves a module by its ID.
	 *
	 * @param string $id The ID of the module.
	 * @return Abstract_Module|null The module if found, null otherwise.
	 */
	public function get( string $id ): ?Abstract_Module {
		return $this->modules[ $id ] ?? null;
	}

	/**
	 * Checks if a module is enabled.
	 *
	 * @param string $id The ID of the module.
	 * @return bool True if the module is enabled, false otherwise.
	 */
	public function is_enabled( string $id ): bool {
		return (bool) ( $this->settings()[ $id ] ?? false );
	}

	/**
	 * Retrieves the current settings for all modules.
	 *
	 * @return array<string, bool> An associative array of module IDs to their enabled status.
	 */
	public function settings(): array {
		$stored = get_option( self::OPTION_KEY, array() );
		if ( ! is_array( $stored ) ) {
			$stored = array();
		}
		$out = array();
		foreach ( $this->modules as $id => $module ) {
			$out[ $id ] = array_key_exists( $id, $stored )
				? (bool) $stored[ $id ]
				: $module->default_enabled();
		}
		return $out;
	}

	/**
	 * Updates the settings for all modules.
	 *
	 * @param array<string, bool> $incoming An associative array of module IDs to their desired enabled status.
	 * @return array<string, bool> The updated settings for all modules.
	 */
	public function update_settings( array $incoming ): array {
		$current = $this->settings();
		foreach ( $incoming as $id => $enabled ) {
			if ( ! isset( $this->modules[ $id ] ) ) {
				continue;
			}
			$was = $current[ $id ] ?? false;
			$now = (bool) $enabled;
			if ( $was && ! $now ) {
				$this->modules[ $id ]->on_disable();
			}
			$current[ $id ] = $now;
		}
		update_option( self::OPTION_KEY, $current, false );
		return $current;
	}

	/**
	 * Map of module dependencies.
	 *
	 * @var array<string, string> Map of module id => id of module it requires.
	 * Populated via `set_dependencies()`.
	 */
	private array $dependencies = array();

	/**
	 * Sets the module dependencies.
	 *
	 * @param array<string, string> $dependencies module id => required id.
	 */
	public function set_dependencies( array $dependencies ): void {
		$this->dependencies = $dependencies;
	}

	/**
	 * Retrieves the current module dependencies.
	 *
	 * @return array<string, string> */
	public function dependencies(): array {
		return $this->dependencies;
	}

	/**
	 * Boots all enabled modules, respecting their dependencies.
	 */
	public function boot_enabled(): void {
		$settings = $this->settings();
		foreach ( $settings as $id => $enabled ) {
			if ( ! $enabled || ! isset( $this->modules[ $id ] ) ) {
				continue;
			}
			// Skip modules whose declared dependency is disabled — keeps the
			// state honest server-side even if the UI ever gets out of sync.
			$required = $this->dependencies[ $id ] ?? '';
			if ( '' !== $required && empty( $settings[ $required ] ) ) {
				continue;
			}
			$this->modules[ $id ]->boot();
		}
	}
}
