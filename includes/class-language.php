<?php
/**
 * Which language of the official notice to show - no WordPress calls.
 *
 * @package degriz-euguarantee
 */

namespace Degriz\Euguarantee;

defined( 'ABSPATH' ) || exit;

class Language {

	/** The 24 official languages in which the Commission publishes the notice. */
	public const AVAILABLE = array( 'bg', 'cs', 'da', 'de', 'el', 'en', 'es', 'et', 'fi', 'fr', 'ga', 'hr', 'hu', 'it', 'lt', 'lv', 'mt', 'nl', 'pl', 'pt', 'ro', 'sk', 'sl', 'sv' );

	/**
	 * The notice language for a WordPress locale such as sl_SI or de_DE_formal; English when the Union does not publish
	 * the language.
	 *
	 * @param string $locale Locale.
	 * @return string
	 */
	public static function for_locale( string $locale ): string {
		$code = strtolower( substr( preg_replace( '/[^A-Za-z_]/', '', $locale ), 0, 2 ) );

		return in_array( $code, self::AVAILABLE, true ) ? $code : 'en';
	}

	/**
	 * The page of the Commission's "Your Europe" portal that the QR code of the notice leads to.
	 *
	 * @param string $language Notice language.
	 * @return string
	 */
	public static function info_url( string $language ): string {
		$language = in_array( $language, self::AVAILABLE, true ) ? $language : 'en';

		return 'https://europa.eu/youreurope/citizens/consumers/shopping/guarantees/index_' . $language . '.htm';
	}
}
