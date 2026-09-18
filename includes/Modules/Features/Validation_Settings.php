<?php
/**
 * Feature: Validation Settings.
 *
 * Ports troychaplin/validation-api-settings and adds automatic per-namespace
 * settings-page generation. Admins can override each registered check's
 * severity (error / warning / disabled).
 *
 * Silently no-ops if the Block Validation Framework module isn't active.
 *
 * @package AccessibilityLab
 */

declare( strict_types = 1 );

namespace AccessibilityLab\Modules\Features;

use AccessibilityLab\Abstracts\Abstract_Module;
use AccessibilityLab\Bucket;
use AccessibilityLab\Credits;
use AccessibilityLab\Track;
use AccessibilityLab\Modules\Features\ValidationSettings\Admin_Pages;
use AccessibilityLab\Modules\Features\ValidationSettings\Level_Override;
use AccessibilityLab\Modules\Features\ValidationSettings\Rest_Controller;

/**
 * Validation Settings module.
 */
final class Validation_Settings extends Abstract_Module {

	/**
	 * Returns the ID for the validation settings module.
	 */
	public function id(): string {
		return 'validation_settings';
	}

	/**
	 * Returns the bucket for the validation settings module.
	 */
	public function bucket(): string {
		return Bucket::FEATURE;
	}

	/**
	 * Returns the track for the validation settings module.
	 */
	public function track(): string {
		return Track::PRACTICAL;
	}

	/**
	 * Returns the name for the validation settings module.
	 */
	public function name(): string {
		return __( 'Validation settings', 'accessibility-lab' );
	}

	/**
	 * Returns the description for the validation settings module.
	 */
	public function description(): string {
		return __( 'Admin UI for overriding the severity of every registered validation check. Adds an auto-generated settings page for each plugin namespace that registers checks. Requires the Block Validation Framework module.', 'accessibility-lab' );
	}

	/**
	 * Returns the credits for the validation settings module.
	 */
	public function credits(): Credits {
		return new Credits(
			author: 'Troy Chaplin',
			source_plugin_slug: 'validation-api-settings',
			source_plugin_url: 'https://github.com/troychaplin/validation-api-settings',
			license: 'GPL-2.0-or-later'
		);
	}

	/**
	 * Boots the validation settings module.
	 */
	public function boot(): void {
		( new Level_Override() )->register();
		( new Rest_Controller() )->register();
		( new Admin_Pages() )->register();
	}
}
