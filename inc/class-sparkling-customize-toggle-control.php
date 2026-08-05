<?php
/**
 * On/off toggle control for the Customizer.
 *
 * Replaces Epsilon_Control_Toggle, which was the theme's only real use of the
 * bundled Epsilon framework. This is a presentation class only: it renders a
 * standard checkbox bound with $this->link(), so the value is stored and
 * sanitised entirely by the WP_Customize_Setting it is attached to. Swapping the
 * control therefore changes no saved data.
 *
 * @package sparkling
 */

if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( class_exists( 'WP_Customize_Control' ) ) {

	/**
	 * Renders a boolean setting as an on/off switch.
	 */
	class Sparkling_Customize_Toggle_Control extends WP_Customize_Control {

		/**
		 * Control type.
		 *
		 * @var string
		 */
		public $type = 'sparkling-toggle';

		/**
		 * Displays the control content.
		 *
		 * Markup and class names are kept identical to the control this replaced so
		 * the Customizer looks and behaves exactly as before.
		 *
		 * @return void
		 */
		public function render_content() {
			?>
			<div class="checkbox_switch">
				<span class="customize-control-title onoffswitch_label"><?php echo esc_html( $this->label ); ?>
					<?php if ( ! empty( $this->description ) ) : ?>
					<i class="dashicons dashicons-editor-help" style="vertical-align: text-bottom; position: relative;">
						<span class="mte-tooltip"><?php echo wp_kses_post( $this->description ); ?></span>
					</i>
					<?php endif; ?>
				</span>
				<div class="onoffswitch">
					<input type="checkbox" id="<?php echo esc_attr( $this->id ); ?>"
						   name="<?php echo esc_attr( $this->id ); ?>" class="onoffswitch-checkbox"
						   value="<?php echo esc_attr( $this->value() ); ?>" <?php $this->link();
							checked( $this->value() ); ?>>
					<label class="onoffswitch-label" for="<?php echo esc_attr( $this->id ); ?>"></label>
				</div>
			</div>
			<?php
		}
	}

	/**
	 * Back-compat shim.
	 *
	 * A child theme may have registered its own controls with the class name the
	 * bundled framework provided. Keep that name resolvable so those child themes
	 * keep working after the framework was removed in 2.6.0.
	 *
	 * @deprecated 2.6.0 Use Sparkling_Customize_Toggle_Control instead.
	 */
	if ( ! class_exists( 'Epsilon_Control_Toggle' ) ) {
		class Epsilon_Control_Toggle extends Sparkling_Customize_Toggle_Control {
		}
	}
}
