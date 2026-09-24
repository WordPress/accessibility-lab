<?php
/**
 * Uninstall handler.
 *
 * @package AccessibilityLab
 */

declare( strict_types = 1 );

defined( 'WP_UNINSTALL_PLUGIN' ) || exit;

require_once __DIR__ . '/includes/autoload.php';

/**
 * Boot the plugin and run uninstall routines for all modules.
 */
$a11y_lab_plugin = \AccessibilityLab\Plugin::instance();
$a11y_lab_plugin->boot(); // Registers modules so on_uninstall() can run.

foreach ( $a11y_lab_plugin->registry->all() as $module ) {
	$module->on_uninstall();
}

delete_option( \AccessibilityLab\Registry::OPTION_KEY );
