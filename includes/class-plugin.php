<?php
/**
 * Plugin bootstrap.
 *
 * @package degriz-euguarantee
 */

namespace Degriz\Euguarantee;

defined( 'ABSPATH' ) || exit;

class Plugin {

	/** @var Plugin|null */
	private static $instance = null;

	/**
	 * @return Plugin
	 */
	public static function instance(): Plugin {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * @return void
	 */
	public function init(): void {
		( new Settings() )->init();
		( new Notice_View() )->init();
		( new Attachment() )->init();
	}
}
