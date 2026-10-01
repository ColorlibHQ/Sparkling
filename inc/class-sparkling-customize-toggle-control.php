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
		 * Class names are kept from the control this replaced, so the switch
		 * looks the same. The description is shown under the switch like core
		 * controls show theirs, instead of in a hover-only tooltip.
		 *
		 * @return void
		 */
		public function render_content() {
			$input_id       = '_customize-input-' . $this->id;
			$label_id       = $input_id . '-label';
			$description_id = $input_id . '-description';
			?>
			<div class="checkbox_switch">
				<span class="customize-control-title onoffswitch_label" id="<?php echo esc_attr( $label_id ); ?>"><?php echo esc_html( $this->label ); ?></span>
				<div class="onoffswitch">
					<?php
					/*
					 * The checkbox is the control. It used to be display:none, so
					 * the switch could not be reached from the keyboard, and it had
					 * no name: the visible label was a <span>, and the <label> it
					 * pointed at was empty. It is visually hidden now, named by
					 * the title and described by the description.
					 */
					?>
					<input type="checkbox" id="<?php echo esc_attr( $input_id ); ?>" class="onoffswitch-checkbox" aria-labelledby="<?php echo esc_attr( $label_id ); ?>"<?php echo $this->description ? ' aria-describedby="' . esc_attr( $description_id ) . '"' : ''; ?> value="<?php echo esc_attr( $this->value() ); ?>" <?php $this->link(); ?> <?php checked( $this->value() ); ?>>
					<label class="onoffswitch-label" for="<?php echo esc_attr( $input_id ); ?>" aria-hidden="true"></label>
				</div>
				<?php if ( ! empty( $this->description ) ) : ?>
					<span id="<?php echo esc_attr( $description_id ); ?>" class="description customize-control-description"><?php echo wp_kses_post( $this->description ); ?></span>
				<?php endif; ?>
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
		class_alias( 'Sparkling_Customize_Toggle_Control', 'Epsilon_Control_Toggle' );
	}
}
