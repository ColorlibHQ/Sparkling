<?php

if ( ! class_exists( 'Sparkling_Notify_System' ) ) {
	/**
	 * Class Sparkling_Notify_System
	 */
	class Sparkling_Notify_System {
		/**
		 * @param $ver
		 *
		 * @return mixed
		 */
		public static function version_check( $ver ) {
			$sparkling = wp_get_theme();

			return version_compare( $sparkling['Version'], $ver, '>=' );
		}

		/**
		 * @return bool
		 */
		public static function is_not_static_page() {
			return 'page' == get_option( 'show_on_front' ) ? true : false;
		}

		/**
		 * @return bool
		 */
		public static function has_widgets() {
			if ( ! is_active_sidebar( 'primary-widgets' ) && ! is_active_sidebar( 'secondary-widgets' ) ) {
				return false;
			}

			return true;
		}

		/**
		 * @return bool
		 */
		public static function has_posts() {

			$imported = get_option( 'sparkling_importded_demo' );
			if ( $imported ) {
				return true;
			}

			$count_posts = wp_count_posts('post');
			if ( $count_posts->publish > 4 ) {
				add_option( 'sparkling_importded_demo', true );
				return true;
			}

			$args  = array( "s" => 'Beautiful Flat Design and Long Shadow Logo Collection' );
			$query = get_posts( $args );
			if ( ! empty( $query ) ) {
				add_option( 'sparkling_importded_demo', true );
				return true;
			}

			return false;
		}

		/**
		 * @return bool
		 */
		public static function has_content() {
			$check = array(
				'widgets' => self::has_widgets(),
				'posts'   => self::has_posts(),
			);

			if ( $check['widgets'] && $check['posts'] ) {
				return true;
			}

			return false;
		}

		/**
		 * @return bool
		 */
		public static function check_wordpress_importer() {
			if ( file_exists( ABSPATH . 'wp-content/plugins/wordpress-importer/wordpress-importer.php' ) ) {
				include_once( ABSPATH . 'wp-admin/includes/plugin.php' );

				return is_plugin_active( 'wordpress-importer/wordpress-importer.php' );
			}

			return false;
		}

		/**
		 * @return bool
		 */
		public static function check_plugin_is_installed( $slug ) {
			if ( file_exists( ABSPATH . 'wp-content/plugins/' . $slug . '/' . $slug . '.php' ) ) {
				return true;
			}

			return false;
		}

		/**
		 * @return bool
		 */
		public static function check_plugin_is_active( $slug ) {
			if ( file_exists( ABSPATH . 'wp-content/plugins/' . $slug . '/' . $slug . '.php' ) ) {
				include_once( ABSPATH . 'wp-admin/includes/plugin.php' );

				return is_plugin_active( $slug . '/' . $slug . '.php' );
			}
		}

		public static function has_plugin( $slug = NULL ){

			$check = array(
				'installed' => self::check_plugin_is_installed( $slug ),
				'active'    => self::check_plugin_is_active( $slug )
			);

			if ( ! $check['installed'] || ! $check['active'] ) {
				return false;
			}

			return true;
			
		}

		public static function has_import_plugin( $slug = NULL ) {
			$return = self::has_content();

			if ( $return ) {
				return true;
			}
			$check = array(
				'installed' => self::check_plugin_is_installed( $slug ),
				'active'    => self::check_plugin_is_active( $slug )
			);

			if ( ! $check['installed'] || ! $check['active'] ) {
				return false;
			}

			return true;
		}

		public static function has_import_content(){

			$plugin = self::has_import_plugin( 'wordpress-importer' );
			$content = self::has_posts();
			if ( !$plugin || !$content ) {
				return false;
			}

			return true;

		}

		public static function has_import_plugins() {
			$check = array(
				'wordpress-importer'       => array( 'installed' => false, 'active' => false ),
				'widget-importer-exporter' => array( 'installed' => false, 'active' => false )
			);

			$content = self::has_content();
			$return  = false;
			if ( $content ) {
				return true;
			}

			$stop = false;
			foreach ( $check as $plugin => $val ) {
				if ( $stop ) {
					continue;
				}

				$check[ $plugin ]['installed'] = self::check_plugin_is_installed( $plugin );
				$check[ $plugin ]['active']    = self::check_plugin_is_active( $plugin );

				if ( ! $check[ $plugin ]['installed'] || ! $check[ $plugin ]['active'] ) {
					$return = true;
					$stop   = true;
				}

			}

			return $return;
		}

		public static function widget_importer_exporter_title() {
			$installed = self::check_plugin_is_installed( 'widget-importer-exporter' );
			if ( ! $installed ) {
				return __( 'Install: Widget Importer Exporter Plugin', 'sparkling' );
			}

			$active = self::check_plugin_is_active( 'widget-importer-exporter' );
			if ( $installed && ! $active ) {
				return __( 'Activate: Widget Importer Exporter Plugin', 'sparkling' );
			}

			return __( 'Install: Widget Importer Exporter Plugin', 'sparkling' );
		}

		public static function wordpress_importer_title() {
			$installed = self::check_plugin_is_installed( 'wordpress-importer' );
			if ( ! $installed ) {
				return __( 'Install: WordPress Importer', 'sparkling' );
			}

			$active = self::check_plugin_is_active( 'wordpress-importer' );
			if ( $installed && ! $active ) {
				return __( 'Activate: WordPress Importer', 'sparkling' );
			}

			return __( 'Install: WordPress Importer', 'sparkling' );
		}

		/**
		 * @return string
		 */
		public static function wordpress_importer_description() {
			$installed = self::check_plugin_is_installed( 'wordpress-importer' );
			if ( ! $installed ) {
				return __( 'Please install the WordPress Importer to create the demo content.', 'sparkling' );
			}

			$active = self::check_plugin_is_active( 'wordpress-importer' );
			if ( $installed && ! $active ) {
				return __( 'Please activate the WordPress Importer to create the demo content.', 'sparkling' );
			}

			return __( 'Please install the WordPress Importer to create the demo content.', 'sparkling' );
		}

		public static function widget_importer_exporter_description() {
			$installed = self::check_plugin_is_installed( 'widget-importer-exporter' );
			if ( ! $installed ) {
				return __( 'Please install the WordPress widget importer to create the demo content', 'sparkling' );
			}

			$active = self::check_plugin_is_active( 'widget-importer-exporter' );
			if ( $installed && ! $active ) {
				return __( 'Please activate the WordPress Widget Importer to create the demo content.', 'sparkling' );
			}

			return __( 'Please install the WordPress widget importer to create the demo content', 'sparkling' );

		}

		public static function create_plugin_requirement_title( $install_text, $activate_text, $plugin_slug ){

			if ( $plugin_slug == '' ) {
				return;
			}
			if ( $install_text == '' && $activate_text = '' ) {
				return;
			}
			if ( $install_text == '' &&  $activate_text != '' ) {
				$install_text = $activate_text;
			}elseif ( $activate_text == '' &&  $install_text != '' ) {
				$activate_text = $install_text;
			}

			$installed = self::check_plugin_is_installed( $plugin_slug );
			if ( ! $installed ) {
				return $install_text;
			}elseif ( ! self::check_plugin_is_active( $plugin_slug ) && $installed ) {
				return $activate_text;
			}else{
				return '';
			}

		}

		/**
		 * @return bool
		 */
		public static function is_not_template_front_page() {
			$page_id = get_option( 'page_on_front' );

			return get_page_template_slug( $page_id ) == 'page-templates/frontpage-template.php' ? true : false;
		}
	}
}