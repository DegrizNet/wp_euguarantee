<?php
/**
 * Settings: storage, defaults, and the page under the WooCommerce menu.
 *
 * @package degriz-euguarantee
 */

namespace Degriz\Euguarantee;

defined( 'ABSPATH' ) || exit;

class Settings {

	public const OPTION = 'degriz_euguarantee_settings';

	/**
	 * Field definitions: key => type, default, label, help, min, max, options.
	 *
	 * @return array<string,array<string,mixed>>
	 */
	public static function fields(): array {
		return array(
			'enabled'  => array(
				'type'    => 'checkbox',
				'default' => 1,
				'label'   => __( 'Show the EU notice on the legal guarantee', 'degriz-euguarantee' ),
			),
			'footer'   => array(
				'type'    => 'checkbox',
				'default' => 1,
				'label'   => __( 'In the footer of every page', 'degriz-euguarantee' ),
			),
			'product'  => array(
				'type'    => 'checkbox',
				'default' => 1,
				'label'   => __( 'On the product page', 'degriz-euguarantee' ),
			),
			'cart'     => array(
				'type'    => 'checkbox',
				'default' => 1,
				'label'   => __( 'In the cart', 'degriz-euguarantee' ),
			),
			'checkout' => array(
				'type'    => 'checkbox',
				'default' => 1,
				'label'   => __( 'Right above the Place order button', 'degriz-euguarantee' ),
			),
			'style'    => array(
				'type'    => 'select',
				'default' => 'button',
				'label'   => __( 'Shown as', 'degriz-euguarantee' ),
				'options' => array(
					'button'  => __( 'A button that opens the notice in a dialog', 'degriz-euguarantee' ),
					'link'    => __( 'A text link that opens the notice', 'degriz-euguarantee' ),
					'picture' => __( 'The notice picture on the page', 'degriz-euguarantee' ),
				),
			),
			'label'    => array(
				'type'    => 'text',
				'default' => '',
				'label'   => __( 'Button or link text', 'degriz-euguarantee' ),
				'help'    => __( 'Empty for "Your rights under the legal guarantee", translated.', 'degriz-euguarantee' ),
			),
			'attach'   => array(
				'type'    => 'checkbox',
				'default' => 1,
				'label'   => __( 'Attach the notice (PDF) to the order confirmation e-mail', 'degriz-euguarantee' ),
				'help'    => __( 'The confirmation has to reach the customer on a durable medium; a link to the shop is not one.', 'degriz-euguarantee' ),
			),
		);
	}

	/**
	 * Default values.
	 *
	 * @return array<string,mixed>
	 */
	public static function defaults(): array {
		return array_map(
			static function ( array $field ) {
				return $field['default'];
			},
			self::fields()
		);
	}

	/**
	 * One setting, with its default.
	 *
	 * @param string $key Setting key.
	 * @return mixed
	 */
	public static function get( string $key ) {
		$saved    = get_option( self::OPTION, array() );
		$defaults = self::defaults();
		if ( is_array( $saved ) && array_key_exists( $key, $saved ) ) {
			return $saved[ $key ];
		}
		return $defaults[ $key ] ?? null;
	}

	/**
	 * @return void
	 */
	public function init(): void {
		add_action( 'admin_menu', array( $this, 'menu' ) );
		add_action( 'admin_init', array( $this, 'register' ) );
	}

	/**
	 * @return void
	 */
	public function menu(): void {
		add_submenu_page(
			'woocommerce',
			__( 'EU guarantee settings', 'degriz-euguarantee' ),
			__( 'EU guarantee settings', 'degriz-euguarantee' ),
			'manage_woocommerce',
			'degriz-euguarantee',
			array( $this, 'render_page' )
		);
	}

	/**
	 * @return void
	 */
	public function register(): void {
		register_setting(
			'degriz_euguarantee',
			self::OPTION,
			array(
				'type'              => 'array',
				'sanitize_callback' => array( $this, 'sanitize' ),
				'default'           => self::defaults(),
			)
		);
	}

	/**
	 * Sanitise the posted settings (unchecked boxes are absent).
	 *
	 * @param mixed $input Posted values.
	 * @return array<string,mixed>
	 */
	public function sanitize( $input ): array {
		$input = is_array( $input ) ? $input : array();
		$out   = array();
		foreach ( self::fields() as $key => $field ) {
			$raw = $input[ $key ] ?? null;
			switch ( $field['type'] ) {
				case 'checkbox':
					$out[ $key ] = empty( $raw ) ? 0 : 1;
					break;
				case 'number':
					$out[ $key ] = min( (float) ( $field['max'] ?? PHP_INT_MAX ), max( (float) ( $field['min'] ?? 0 ), (float) ( null === $raw ? $field['default'] : $raw ) ) );
					break;
				case 'select':
					$out[ $key ] = array_key_exists( (string) $raw, $field['options'] ) ? (string) $raw : $field['default'];
					break;
				case 'textarea':
					$out[ $key ] = sanitize_textarea_field( (string) $raw );
					break;
				case 'email':
					$out[ $key ] = sanitize_email( (string) $raw );
					break;
				default:
					$out[ $key ] = sanitize_text_field( (string) $raw );
			}
		}
		return $out;
	}

	/**
	 * Settings page.
	 *
	 * @return void
	 */
	public function render_page(): void {
		if ( ! current_user_can( 'manage_woocommerce' ) ) {
			return;
		}
		?>
		<div class="wrap">
			<h1><?php echo esc_html__( 'EU guarantee settings', 'degriz-euguarantee' ); ?></h1>
			<?php settings_errors(); ?>
			<div class="notice notice-warning inline"><p><?php echo esc_html__( 'Disclaimer: the notice is the official EU text (Commission Implementing Regulation (EU) 2025/1960) as of 19 November 2026 and is provided for information only. You alone are responsible for the correctness, completeness and legal compliance of what your shop shows. This is not legal advice. The plugin is provided "as is", without warranty of any kind, and its author is not liable for any damage arising from its use.', 'degriz-euguarantee' ); ?></p></div>

			<form method="post" action="options.php">
				<?php settings_fields( 'degriz_euguarantee' ); ?>
				<table class="form-table" role="presentation">
					<?php foreach ( self::fields() as $key => $field ) : ?>
						<tr>
							<th scope="row"><label for="<?php echo esc_attr( 'deg-' . $key ); ?>"><?php echo esc_html( $field['label'] ); ?></label></th>
							<td>
								<?php $this->render_field( $key, $field ); ?>
								<?php if ( ! empty( $field['help'] ) ) : ?>
									<p class="description"><?php echo esc_html( $field['help'] ); ?></p>
								<?php endif; ?>
							</td>
						</tr>
					<?php endforeach; ?>
				</table>
				<?php submit_button(); ?>
			</form>

		</div>
		<?php
	}

	/**
	 * One input.
	 *
	 * @param string              $key   Setting key.
	 * @param array<string,mixed> $field Field definition.
	 * @return void
	 */
	private function render_field( string $key, array $field ): void {
		$id    = 'deg-' . $key;
		$name  = self::OPTION . '[' . $key . ']';
		$value = self::get( $key );
		switch ( $field['type'] ) {
			case 'checkbox':
				?>
				<label><input id="<?php echo esc_attr( $id ); ?>" type="checkbox" name="<?php echo esc_attr( $name ); ?>" value="1" <?php checked( (int) $value, 1 ); ?>> <?php esc_html_e( 'Enabled', 'degriz-euguarantee' ); ?></label>
				<?php
				break;
			case 'number':
				?>
				<input id="<?php echo esc_attr( $id ); ?>" type="number" class="small-text" step="any" min="<?php echo esc_attr( (string) ( $field['min'] ?? 0 ) ); ?>" max="<?php echo esc_attr( (string) ( $field['max'] ?? '' ) ); ?>" name="<?php echo esc_attr( $name ); ?>" value="<?php echo esc_attr( (string) $value ); ?>">
				<?php
				break;
			case 'select':
				?>
				<select id="<?php echo esc_attr( $id ); ?>" name="<?php echo esc_attr( $name ); ?>">
					<?php foreach ( $field['options'] as $option => $label ) : ?>
						<option value="<?php echo esc_attr( (string) $option ); ?>" <?php selected( (string) $value, (string) $option ); ?>><?php echo esc_html( $label ); ?></option>
					<?php endforeach; ?>
				</select>
				<?php
				break;
			case 'textarea':
				?>
				<textarea id="<?php echo esc_attr( $id ); ?>" class="large-text" rows="4" name="<?php echo esc_attr( $name ); ?>"><?php echo esc_textarea( (string) $value ); ?></textarea>
				<?php
				break;
			default:
				?>
				<input id="<?php echo esc_attr( $id ); ?>" type="<?php echo 'email' === $field['type'] ? 'email' : 'text'; ?>" class="regular-text" name="<?php echo esc_attr( $name ); ?>" value="<?php echo esc_attr( (string) $value ); ?>">
				<?php
		}
	}
}
