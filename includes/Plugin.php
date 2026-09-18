<?php
/**
 * Plugin bootstrapper.
 *
 * @package AccessibilityLab
 */

declare( strict_types = 1 );

namespace AccessibilityLab;

use AccessibilityLab\Admin\Settings_Page;
use AccessibilityLab\Modules\Experiments\Block_Validation_Framework;
use AccessibilityLab\Modules\Features\Core_Block_Validation_Rules;
use AccessibilityLab\Modules\Features\Media_Library_View_Config;
use AccessibilityLab\Modules\Features\Validation_Settings;
use AccessibilityLab\REST\Modules_Controller;

/**
 * Main plugin class.
 */
final class Plugin {

	/**
	 * The singleton instance of the plugin.
	 *
	 * @var Plugin|null
	 */
	private static ?Plugin $instance = null;

	/**
	 * The registry instance.
	 *
	 * @var Registry The registry instance.
	 */
	public readonly Registry $registry;

	/**
	 * Returns the singleton instance of the plugin.
	 */
	public static function instance(): self {
		return self::$instance ??= new self();
	}

	/**
	 * Constructor.
	 */
	private function __construct() {
		$this->registry = new Registry();
	}

	/**
	 * Boots the plugin.
	 */
	public function boot(): void {
		$this->register_first_party_modules();
		$this->registry->set_dependencies(
			array(
				'core_block_validation_rules' => 'block_validation_framework',
				'validation_settings'         => 'block_validation_framework',
			)
		);

		/**
		 * Fires when third parties may register their own modules.
		 *
		 * @param Registry $registry
		 */
		do_action( 'accessibility_lab_register_modules', $this->registry );

		$this->registry->boot_enabled();

		( new Settings_Page( $this->registry ) )->register();
		( new Modules_Controller( $this->registry ) )->register();

		// Register the modules-settings option with core so it's visible
		// via `GET /wp/v2/settings`, has a sanitize callback that runs on
		// every write path (not just our REST endpoint), and shows up in
		// WP-CLI. The schema mirrors what the Registry actually stores.
		add_action( 'init', array( $this, 'register_option' ) );
	}

	/**
	 * Registers the plugin option with WordPress.
	 */
	public function register_option(): void {
		register_setting(
			'accessibility_lab',
			Registry::OPTION_KEY,
			array(
				'type'              => 'object',
				'description'       => __( 'Which Accessibility Lab modules are enabled.', 'accessibility-lab' ),
				'default'           => array(),
				'show_in_rest'      => false, // Written via our own controller; hide from generic settings endpoint.
				'sanitize_callback' => static function ( $value ): array {
					if ( ! is_array( $value ) ) {
						return array();
					}
					$out = array();
					foreach ( $value as $id => $enabled ) {
						$out[ (string) $id ] = (bool) $enabled;
					}
					return $out;
				},
			)
		);
	}

	/**
	 * Registers the first-party modules with the registry.
	 */
	private function register_first_party_modules(): void {
		$this->registry->register( new Media_Library_View_Config() );
		$this->registry->register( new Block_Validation_Framework() );
		$this->registry->register( new Core_Block_Validation_Rules() );
		$this->registry->register( new Validation_Settings() );
	}
}
