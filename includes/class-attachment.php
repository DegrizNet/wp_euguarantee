<?php
/**
 * The notice (PDF) as an attachment of the order confirmation e-mail.
 *
 * @package degriz-euguarantee
 */

namespace Degriz\Euguarantee;

defined( 'ABSPATH' ) || exit;

class Attachment {

	/**
	 * @return void
	 */
	public function init(): void {
		add_filter( 'woocommerce_email_attachments', array( $this, 'attach' ), 10, 3 );
	}

	/**
	 * @param array             $attachments Files.
	 * @param string            $email_id    E-mail id.
	 * @param \WC_Order|mixed   $subject_order      Order (or other object).
	 * @return array
	 */
	public function attach( $attachments, $email_id, $subject_order ) {
		if ( ! Settings::get( 'enabled' ) || ! Settings::get( 'attach' ) || 'customer_processing_order' !== $email_id || ! $subject_order instanceof \WC_Order ) {
			return $attachments;
		}
		$lang = Language::for_locale( $subject_order->get_meta( 'wpml_language' ) ? (string) $subject_order->get_meta( 'wpml_language' ) : determine_locale() );
		$file = DEGRIZ_EUGUARANTEE_DIR . 'assets/notice/legal-guarantee-notice-' . $lang . '.pdf';
		if ( is_file( $file ) ) {
			$attachments[] = $file;
		}

		return $attachments;
	}
}
