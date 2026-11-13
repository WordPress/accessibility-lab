<?php
/**
 * Base class for every Accessibility Lab module.
 *
 * Each module answers two orthogonal questions:
 *
 * - bucket(): FEATURE or EXPERIMENT — shape/maturity.
 * - track():  CORE_TRACK or PRACTICAL — intent to reach WordPress Core.
 *
 * Credits are optional metadata; attach them when the module was adopted from
 * a community source, regardless of bucket or track.
 *
 * @package AccessibilityLab
 */

declare( strict_types = 1 );

namespace AccessibilityLab\Abstracts;

use AccessibilityLab\Credits;
use AccessibilityLab\Track;

/**
 * Abstract base class for Accessibility Lab modules.
 */
abstract class Abstract_Module {

	/**
	 * Returns the unique identifier for the module.
	 *
	 * @return string
	 */
	abstract public function id(): string;

	/**
	 * Returns the bucket of the module.
	 *
	 * @return string Bucket::FEATURE or Bucket::EXPERIMENT.
	 */
	abstract public function bucket(): string;

	/**
	 * Returns the track of the module.
	 *
	 * @return string Track::CORE_TRACK or Track::PRACTICAL.
	 *
	 * Default: PRACTICAL. Override in modules whose reason for existing is
	 * to gather data toward a Core proposal.
	 */
	public function track(): string {
		return Track::PRACTICAL;
	}

	/**
	 * Returns the name of the module.
	 *
	 * @return string
	 */
	abstract public function name(): string;

	/**
	 * Returns the description for the module.
	 *
	 * @return string
	 */
	abstract public function description(): string;

	/**
	 * Indicates whether the module is enabled by default.
	 *
	 * @return bool
	 */
	public function default_enabled(): bool {
		return false;
	}

	/**
	 * Returns the credits for the module, if any.
	 *
	 * @return Credits|null
	 */
	public function credits(): ?Credits {
		return null;
	}

	/**
	 * Register hooks. Only called when the module is enabled.
	 */
	abstract public function boot(): void;

	/**
	 * Called when the user turns the module off. MUST leave the site working.
	 */
	public function on_disable(): void {}

	/**
	 * Called from uninstall.php. Remove options / drop-ins / custom tables.
	 */
	public function on_uninstall(): void {}
}
