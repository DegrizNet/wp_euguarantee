<?php
/**
 * Uninstall cleanup.
 *
 * @package degriz-euguarantee
 */

defined( 'WP_UNINSTALL_PLUGIN' ) || exit;

delete_option( 'degriz_euguarantee_settings' );
