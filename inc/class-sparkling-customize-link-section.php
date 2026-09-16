<?php
/**
 * A Customizer section that is a link rather than a panel.
 *
 * Replaces Epsilon_Section_Recommended_Actions and Epsilon_Section_Pro. Both
 * were instantiated in inc/welcome-screen/welcome-page-setup.php and both went
 * with the Epsilon framework in 2.6.0, which left that file constructing
 * classes that no longer existed -- so opening the Customizer raised
 * "Class Epsilon_Section_Recommended_Actions not found" and the screen died
 * before it rendered.
 *
 * WordPress has no built-in section that behaves this way, so this is the same
 * shape core uses for its own custom sections: a WP_Customize_Section subclass
 * with a JS template. It must be registered with register_section_type() or the
 * template is never printed and the section silently does not appear.
 *
 * @package sparkling
 */

if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( class_exists( 'WP_Customize_Section' ) && ! class_exists( 'Sparkling_Customize_Link_Section' ) ) {

	/**
	 * Renders a section title that opens a URL instead of a panel.
	 */
	class Sparkling_Customize_Link_Section extends WP_Customize_Section {

		/**
		 * Section type.
		 *
		 * @var string
		 */
		public $type = 'sparkling-link';

		/**
		 * Where the section sends the user.
		 *
		 * @var string
		 */
		public $url = '';

		/**
		 * Add the URL to what is handed to the JS template.
		 *
		 * @return array
		 */
		public function json() {
			$json = parent::json();

			// Raw, not esc_url(): that entity-encodes & for HTML, and the {{ }} template
			// escapes it again, which turned &tab= into a literal &#038;tab= in the link.
			$json['url'] = esc_url_raw( $this->url );

			return $json;
		}

		/**
		 * The Underscore template for one section.
		 *
		 * @return void
		 */
		protected function render_template() {
			?>
			<li id="accordion-section-{{ data.id }}" class="sparkling-link-section accordion-section">
				<h3 class="accordion-section-title">
					<a href="{{ data.url }}" target="_blank" rel="noopener noreferrer">
						{{ data.title }}
						<span class="screen-reader-text"><?php esc_html_e( '(opens in a new tab)', 'sparkling' ); ?></span>
					</a>
				</h3>
			</li>
			<?php
		}
	}
}
