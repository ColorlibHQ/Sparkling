<?php
/**
 * Appearance > About Sparkling.
 *
 * @package sparkling
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Sparkling_Welcome' ) ) :

	/**
	 * Welcome Screen Class
	 *
	 * The "recommended actions" machinery this class used to carry -- a tab, two
	 * dismiss AJAX handlers, an action counter for the menu badge and an AJAX
	 * handler that set the front page to a page titled "Homepage" -- was removed
	 * in 2.7.0. The theme defines no recommended actions, so the counter was
	 * always 0, nothing linked to the tab, and opening it was a fatal error
	 * ("Using $this when not in object context").
	 */
	class Sparkling_Welcome {

		/**
		 * The About page's tabs: slug => template part.
		 *
		 * @var array
		 */
		private $tabs = array(
			'getting_started'     => 'getting-started',
			'recommended_plugins' => 'recommended-plugins',
			'changelog'           => 'changelog',
			'support'             => 'support',
		);

		/**
		 * Constructor for the welcome screen
		 */
		public function __construct() {
			add_action( 'admin_menu', array( $this, 'sparkling_welcome_register_menu' ) );
			add_action( 'load-themes.php', array( $this, 'sparkling_activation_admin_notice' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'sparkling_welcome_style_and_scripts' ) );
			add_filter( 'admin_body_class', array( $this, 'sparkling_admin_body_class' ) );
		}

		/**
		 * The tab being viewed.
		 *
		 * @return string
		 */
		private function current_tab() {
			// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Read-only navigation.
			$tab = isset( $_GET['tab'] ) ? sanitize_key( wp_unslash( $_GET['tab'] ) ) : 'getting_started';

			return isset( $this->tabs[ $tab ] ) ? $tab : 'getting_started';
		}

		/**
		 * Borrow core's plugin-install body class on the tab that renders plugin cards.
		 *
		 * Core's layout for .plugin-card is scoped to the .plugin-install-php body
		 * class, which WordPress only sets on plugin-install.php:
		 *
		 *     .plugin-install-php #the-list    { display: flex; flex-wrap: wrap; }
		 *     .plugin-install-php .plugin-card { display: flex; flex-direction: column;
		 *                                        justify-content: space-between; }
		 *
		 * Without it the floated cards never clear, so #the-list collapses to zero
		 * height and everything after it overlaps. Adding the class gives us core's
		 * flex grid and equal-height cards without the theme shipping any CSS.
		 *
		 * @param string $classes Space-separated admin body classes.
		 * @return string
		 */
		public function sparkling_admin_body_class( $classes ) {
			$screen = get_current_screen();

			if ( $screen && 'appearance_page_sparkling-welcome' === $screen->id && 'recommended_plugins' === $this->current_tab() ) {
				$classes .= ' plugin-install-php';
			}

			return $classes;
		}

		/**
		 * Creates the dashboard page
		 *
		 * @see   add_theme_page()
		 * @since 1.8.2.4
		 */
		public function sparkling_welcome_register_menu() {
			$title = __( 'About Sparkling', 'sparkling' );

			add_theme_page( $title, $title, 'edit_theme_options', 'sparkling-welcome', array( $this, 'sparkling_welcome_screen' ) );
		}

		/**
		 * Adds an admin notice upon successful activation.
		 *
		 * @since 1.8.2.4
		 */
		public function sparkling_activation_admin_notice() {
			// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Core's own "activated" flag.
			if ( isset( $_GET['activated'] ) && current_user_can( 'edit_theme_options' ) ) {
				add_action( 'admin_notices', array( $this, 'sparkling_welcome_admin_notice' ), 99 );
			}
		}

		/**
		 * Display an admin notice linking to the welcome screen
		 *
		 * @since 1.8.2.4
		 */
		public function sparkling_welcome_admin_notice() {
			?>
			<div class="notice notice-success is-dismissible">
				<p>
					<?php
					printf(
						/* translators: 1: opening anchor tag, 2: closing anchor tag */
						esc_html__( 'Welcome! Thank you for choosing Sparkling! To fully take advantage of the best our theme can offer please make sure you visit our %1$swelcome page%2$s.', 'sparkling' ),
						'<a href="' . esc_url( admin_url( 'themes.php?page=sparkling-welcome' ) ) . '">',
						'</a>'
					);
					?>
				</p>
				<p><a href="<?php echo esc_url( admin_url( 'themes.php?page=sparkling-welcome' ) ); ?>" class="button" style="text-decoration: none;"><?php esc_html_e( 'Get started with Sparkling', 'sparkling' ); ?></a></p>
			</div>
			<?php
		}

		/**
		 * Load welcome screen css, on the welcome page only.
		 *
		 * @since 1.8.2.4
		 *
		 * @param string $hook_suffix Current admin page.
		 */
		public function sparkling_welcome_style_and_scripts( $hook_suffix ) {
			if ( 'appearance_page_sparkling-welcome' !== $hook_suffix ) {
				return;
			}

			wp_enqueue_style( 'sparkling-welcome-screen-css', get_template_directory_uri() . '/inc/welcome-screen/css/welcome.css', array(), SPARKLING_VERSION );

			/*
			 * The recommended-plugins tab renders core's plugin-card markup and
			 * install buttons, which core's own scripts drive.
			 */
			if ( 'recommended_plugins' === $this->current_tab() ) {
				wp_enqueue_style( 'plugin-install' );
				wp_enqueue_script( 'plugin-install' );
				wp_enqueue_script( 'updates' );
				add_thickbox();
			}
		}

		/**
		 * Call plugin API to get plugins info
		 *
		 * @param string $slug Plugin slug on WordPress.org.
		 * @return object|WP_Error
		 */
		public function call_plugin_api( $slug ) {
			include_once ABSPATH . 'wp-admin/includes/plugin-install.php';
			$call_api = get_transient( 'sparkling_plugin_information_transient_' . $slug );
			if ( false === $call_api ) {
				$call_api = plugins_api(
					'plugin_information',
					array(
						'slug'   => $slug,
						'fields' => array(
							'downloaded'        => false,
							'rating'            => false,
							'description'       => false,
							'short_description' => true,
							'donate_link'       => false,
							'tags'              => false,
							'sections'          => true,
							'homepage'          => true,
							'added'             => false,
							'last_updated'      => false,
							'compatibility'     => false,
							'tested'            => false,
							'requires'          => false,
							'downloadlink'      => false,
							'icons'             => true,
						),
					)
				);
				set_transient( 'sparkling_plugin_information_transient_' . $slug, $call_api, 30 * MINUTE_IN_SECONDS );
			}

			return $call_api;
		}

		/**
		 * The installed plugin file for a WordPress.org slug, e.g. "kali-forms/kali-forms.php".
		 *
		 * This used to assume ABSPATH . 'wp-content/plugins/<slug>/<slug>.php', which
		 * is wrong for a relocated content directory and for any plugin whose main
		 * file is named differently -- FancyBox for WordPress is fancybox.php -- so
		 * those always showed "Install" even when installed and active.
		 *
		 * @param string $slug Plugin slug, which is its directory name.
		 * @return string Plugin file relative to the plugins directory, or ''.
		 */
		public function plugin_file( $slug ) {
			include_once ABSPATH . 'wp-admin/includes/plugin.php';

			foreach ( array_keys( get_plugins( '/' . $slug ) ) as $file ) {
				return $slug . '/' . $file;
			}

			return '';
		}

		/**
		 * Check if a plugin is installed and active.
		 *
		 * @param string $slug Plugin slug.
		 * @return array {status: bool active, needs: install|activate|deactivate, file: string}
		 */
		public function check_active( $slug ) {
			$file = $this->plugin_file( $slug );

			if ( '' === $file ) {
				return array(
					'status' => false,
					'needs'  => 'install',
					'file'   => '',
				);
			}

			$active = is_plugin_active( $file );

			return array(
				'status' => $active,
				'needs'  => $active ? 'deactivate' : 'activate',
				'file'   => $file,
			);
		}

		/**
		 * Check for icon
		 *
		 * @param array $arr Icons from the plugins API.
		 * @return string
		 */
		public function check_for_icon( $arr ) {
			if ( ! empty( $arr['svg'] ) ) {
				$plugin_icon_url = $arr['svg'];
			} elseif ( ! empty( $arr['2x'] ) ) {
				$plugin_icon_url = $arr['2x'];
			} elseif ( ! empty( $arr['1x'] ) ) {
				$plugin_icon_url = $arr['1x'];
			} else {
				$plugin_icon_url = isset( $arr['default'] ) ? $arr['default'] : '';
			}

			return $plugin_icon_url;
		}

		/**
		 * Create action link
		 *
		 * @param string $state install, activate or deactivate.
		 * @param string $slug  Plugin slug.
		 * @return string
		 */
		public function create_action_link( $state, $slug ) {
			$file = $this->plugin_file( $slug );

			switch ( $state ) {
				case 'install':
					return wp_nonce_url(
						add_query_arg(
							array(
								'action' => 'install-plugin',
								'plugin' => $slug,
							),
							network_admin_url( 'update.php' )
						),
						'install-plugin_' . $slug
					);
				case 'deactivate':
				case 'activate':
					return add_query_arg(
						array(
							'action'        => $state,
							'plugin'        => rawurlencode( $file ),
							'plugin_status' => 'all',
							'paged'         => '1',
							'_wpnonce'      => wp_create_nonce( $state . '-plugin_' . $file ),
						),
						network_admin_url( 'plugins.php' )
					);
			}

			return '';
		}

		/**
		 * Welcome screen content
		 *
		 * @since 1.8.2.4
		 */
		public function sparkling_welcome_screen() {
			if ( ! current_user_can( 'edit_theme_options' ) ) {
				wp_die( esc_html__( 'You are not allowed to access this page.', 'sparkling' ) );
			}

			$sparkling  = wp_get_theme( get_template() ); // The parent: a child theme would report its own version.
			$active_tab = $this->current_tab();
			$labels     = array(
				'getting_started'     => __( 'Getting Started', 'sparkling' ),
				'recommended_plugins' => __( 'Recommended Plugins', 'sparkling' ),
				'changelog'           => __( 'Changelog', 'sparkling' ),
				'support'             => __( 'Support', 'sparkling' ),
			);
			?>

			<div class="wrap about-wrap epsilon-wrap">

				<h1><?php echo esc_html( __( 'Welcome to Sparkling! - Version ', 'sparkling' ) . $sparkling->get( 'Version' ) ); ?></h1>

				<div class="about-text"><?php esc_html_e( 'Sparkling is now installed and ready to use! Get ready to build something beautiful. We hope you enjoy it! We want to make sure you have the best experience using Sparkling and that is why we gathered here all the necessary information for you. We hope you will enjoy using Sparkling, as much as we enjoy creating great products.', 'sparkling' ); ?></div>

				<div class="wp-badge epsilon-welcome-logo"></div>

				<nav class="nav-tab-wrapper wp-clearfix" aria-label="<?php esc_attr_e( 'About Sparkling', 'sparkling' ); ?>">
					<?php foreach ( $labels as $tab => $label ) : ?>
						<a href="<?php echo esc_url( admin_url( 'themes.php?page=sparkling-welcome&tab=' . $tab ) ); ?>" class="nav-tab<?php echo $tab === $active_tab ? ' nav-tab-active' : ''; ?>"<?php echo $tab === $active_tab ? ' aria-current="page"' : ''; ?>><?php echo esc_html( $label ); ?></a>
					<?php endforeach; ?>
				</nav>

				<?php
				/*
				 * The plugin tab renders core's plugin-card markup, which core styles
				 * for a plain .wrap page. about.css restyles p, h3 and img for
				 * everything inside .about-wrap and loads after list-tables.css, so
				 * nesting the cards here inflated their type and blew the card heights
				 * out. Close .about-wrap after the tab nav and let that tab render in
				 * the context core designed the component for.
				 */
				if ( 'recommended_plugins' === $active_tab ) {
					echo '</div><div class="wrap sparkling-welcome-plugins">';
				}

				get_template_part( 'inc/welcome-screen/sections/' . $this->tabs[ $active_tab ] );
				?>

			</div><!--/.wrap.about-wrap-->

			<?php
		}
	}

endif;
