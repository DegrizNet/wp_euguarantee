<?php
/**
 * Showing the notice: footer, product page, cart, above Place order.
 *
 * @package degriz-euguarantee
 */

namespace Degriz\Euguarantee;

defined( 'ABSPATH' ) || exit;

class Notice_View {

	/** @var int Counter for unique dialog ids. */
	private static $count = 0;

	/** @var array<string,bool> Places printed already on this request. */
	private $done = array();

	/**
	 * @return void
	 */
	public function init(): void {
		add_action( 'wp_footer', array( $this, 'footer' ), 5 );
		add_action( 'woocommerce_after_add_to_cart_form', array( $this, 'classic_product' ), 25 );
		add_action( 'woocommerce_after_cart', array( $this, 'classic_cart' ) );
		add_action( 'woocommerce_review_order_before_submit', array( $this, 'classic_checkout' ) );
		add_filter( 'render_block', array( $this, 'blocks' ), 10, 2 );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue' ) );
	}

	/**
	 * The language of the notice for this request.
	 *
	 * @return string
	 */
	public static function language(): string {
		return Language::for_locale( determine_locale() );
	}

	/**
	 * @return void
	 */
	public function enqueue(): void {
		if ( ! Settings::get( 'enabled' ) ) {
			return;
		}
		wp_enqueue_style( 'degriz-euguarantee', DEGRIZ_EUGUARANTEE_URL . 'assets/css/frontend.css', array(), DEGRIZ_EUGUARANTEE_VERSION );
		wp_enqueue_script( 'degriz-euguarantee', DEGRIZ_EUGUARANTEE_URL . 'assets/js/notice.js', array(), DEGRIZ_EUGUARANTEE_VERSION, true );
	}

	/**
	 * The label of the button or link.
	 *
	 * @return string
	 */
	public static function label(): string {
		$label = trim( (string) Settings::get( 'label' ) );

		return '' !== $label ? $label : __( 'Your rights under the legal guarantee', 'degriz-euguarantee' );
	}

	/**
	 * URL of an official file.
	 *
	 * @param string $ext png or pdf.
	 * @return string
	 */
	public static function file_url( string $ext ): string {
		$lang = self::language();

		return DEGRIZ_EUGUARANTEE_URL . 'assets/notice/' . ( 'pdf' === $ext ? 'legal-guarantee-notice-' . $lang . '.pdf' : $lang . '.png' );
	}

	/**
	 * The HTML of the notice for a place.
	 *
	 * @param string $place Place name (footer, product, cart, checkout).
	 * @return string
	 */
	public static function html( string $place ): string {
		if ( ! Settings::get( 'enabled' ) ) {
			return '';
		}
		$lang  = self::language();
		$info  = Language::info_url( $lang );
		$pdf   = self::file_url( 'pdf' );
		$image = self::file_url( 'png' );
		$style = (string) Settings::get( 'style' );
		if ( 'picture' === $style ) {
			return '<div class="deg deg--picture deg--' . esc_attr( $place ) . '"><img class="deg__img" src="' . esc_url( $image ) . '" alt="' . esc_attr__( 'EU notice on the legal guarantee of conformity', 'degriz-euguarantee' ) . '" loading="lazy"><p class="deg__more"><a href="' . esc_url( $info ) . '" target="_blank" rel="noopener">' . esc_html__( 'Link to further information on the legal guarantee', 'degriz-euguarantee' ) . '</a></p></div>';
		}
		++self::$count;
		$id    = 'deg-dialog-' . self::$count;
		$class = 'link' === $style ? 'deg__link' : 'deg__button';

		return '<div class="deg deg--' . esc_attr( $place ) . '"><a class="' . esc_attr( $class ) . '" href="' . esc_url( $pdf ) . '" target="_blank" rel="noopener" data-deg-open="' . esc_attr( $id ) . '">' . esc_html( self::label() ) . '</a>'
			. '<dialog class="deg__dialog" id="' . esc_attr( $id ) . '" aria-label="' . esc_attr__( 'EU notice on the legal guarantee', 'degriz-euguarantee' ) . '"><form method="dialog"><button class="deg__close" aria-label="' . esc_attr__( 'Close', 'degriz-euguarantee' ) . '">&times;</button></form>'
			. '<img class="deg__img" src="' . esc_url( $image ) . '" alt="' . esc_attr__( 'EU notice on the legal guarantee of conformity', 'degriz-euguarantee' ) . '" loading="lazy">'
			. '<p class="deg__more"><a href="' . esc_url( $pdf ) . '" target="_blank" rel="noopener">' . esc_html__( 'Open as PDF', 'degriz-euguarantee' ) . '</a> &middot; <a href="' . esc_url( $info ) . '" target="_blank" rel="noopener">' . esc_html__( 'Link to further information on the legal guarantee', 'degriz-euguarantee' ) . '</a></p></dialog></div>';
	}

	/**
	 * Print a place once per request.
	 *
	 * @param string $place Place.
	 * @param string $key   Setting key.
	 * @return string
	 */
	private function once( string $place, string $key ): string {
		if ( ! Settings::get( $key ) || ! empty( $this->done[ $place ] ) ) {
			return '';
		}
		$this->done[ $place ] = true;

		return self::html( $place );
	}

	/**
	 * @return void
	 */
	public function footer(): void {
		if ( is_admin() ) {
			return;
		}
		echo $this->once( 'footer', 'footer' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped while building.
	}

	/**
	 * @return void
	 */
	public function classic_product(): void {
		echo $this->once( 'product', 'product' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped while building.
	}

	/**
	 * @return void
	 */
	public function classic_cart(): void {
		echo $this->once( 'cart', 'cart' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped while building.
	}

	/**
	 * @return void
	 */
	public function classic_checkout(): void {
		echo $this->once( 'checkout', 'checkout' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped while building.
	}

	/**
	 * Block themes.
	 *
	 * @param string $content Block HTML.
	 * @param array  $block   Parsed block.
	 * @return string
	 */
	public function blocks( $content, $block ) {
		$name = $block['blockName'] ?? '';
		if ( 'woocommerce/add-to-cart-form' === $name && is_singular( 'product' ) ) {
			return $content . $this->once( 'product', 'product' );
		}
		if ( 'woocommerce/cart' === $name ) {
			return $content . $this->once( 'cart', 'cart' );
		}
		if ( 'woocommerce/checkout-actions-block' === $name ) {
			return $this->once( 'checkout', 'checkout' ) . $content;
		}

		return $content;
	}
}
