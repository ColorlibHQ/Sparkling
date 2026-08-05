<?php
/**
 * Recommended Plugins
 *
 * Uses core's own plugin-card markup (the same structure wp-admin renders on
 * Plugins > Add New) so the layout, grid, buttons and responsive behaviour all
 * come from core's plugin-install stylesheet, which Sparkling_Welcome enqueues
 * on admin_enqueue_scripts. The theme adds no CSS of its own for this tab.
 */

global $sparkling_recommended_plugins;

// Initialize the Sparkling_Welcome class
$sparkling_welcome = new Sparkling_Welcome();

// Get the required functions from the class
$call_plugin_api    = array( $sparkling_welcome, 'call_plugin_api' );
$check_for_icon     = array( $sparkling_welcome, 'check_for_icon' );
$check_active       = array( $sparkling_welcome, 'check_active' );
$create_action_link = array( $sparkling_welcome, 'create_action_link' );

?>

<div class="wp-list-table widefat plugin-install">
	<h2 class="screen-reader-text"><?php esc_html_e( 'Recommended plugins list', 'sparkling' ); ?></h2>

	<div id="the-list">
		<?php
		foreach ( $sparkling_recommended_plugins as $plugin => $prop ) {

			$info = call_user_func( $call_plugin_api, $plugin );

			/*
			 * plugins_api() returns WP_Error when wordpress.org cannot be reached.
			 * Previously the properties below were read straight off it, so an
			 * offline site rendered a broken tab.
			 */
			if ( is_wp_error( $info ) || empty( $info->name ) ) {
				continue;
			}

			$icon   = call_user_func( $check_for_icon, $info->icons );
			$active = call_user_func( $check_active, $plugin );
			$url    = call_user_func( $create_action_link, $active['needs'], $plugin );

			$is_active = ( 'install' !== $active['needs'] && $active['status'] );

			switch ( $active['needs'] ) {
				case 'install':
					$class = 'install-now button';
					$label = __( 'Install Now', 'sparkling' );
					break;
				case 'activate':
					$class = 'activate-now button button-primary';
					$label = __( 'Activate', 'sparkling' );
					break;
				default:
					$class = 'button';
					$label = __( 'Deactivate', 'sparkling' );
					break;
			}

			$details_url = add_query_arg(
				array(
					'tab'       => 'plugin-information',
					'plugin'    => $plugin,
					'TB_iframe' => 'true',
					'width'     => 600,
					'height'    => 550,
				),
				self_admin_url( 'plugin-install.php' )
			);
			?>
			<div class="plugin-card plugin-card-<?php echo esc_attr( sanitize_html_class( $plugin ) ); ?>">
				<div class="plugin-card-top">
					<div class="name column-name">
						<h3>
							<a href="<?php echo esc_url( $details_url ); ?>" class="thickbox open-plugin-details-modal">
								<?php echo esc_html( $info->name ); ?>
								<img src="<?php echo esc_url( $icon ); ?>" class="plugin-icon" alt="" />
							</a>
						</h3>
					</div>

					<div class="action-links">
						<ul class="plugin-action-buttons">
							<li>
								<?php if ( $is_active ) : ?>
									<button type="button" class="button button-disabled" disabled="disabled"><?php esc_html_e( 'Active', 'sparkling' ); ?></button>
								<?php else : ?>
									<a data-slug="<?php echo esc_attr( $plugin ); ?>"
										class="<?php echo esc_attr( $class ); ?>"
										href="<?php echo esc_url( $url ); ?>"><?php echo esc_html( $label ); ?></a>
								<?php endif; ?>
							</li>
							<li>
								<a href="<?php echo esc_url( $details_url ); ?>" class="thickbox open-plugin-details-modal">
									<?php esc_html_e( 'More Details', 'sparkling' ); ?>
								</a>
							</li>
						</ul>
					</div>

					<div class="desc column-description">
						<p><?php echo wp_kses_post( $info->short_description ); ?></p>
						<p class="authors"><cite><?php
							/* translators: %s: plugin author name */
							printf( esc_html__( 'By %s', 'sparkling' ), wp_kses_post( $info->author ) );
						?></cite></p>
					</div>
				</div>

				<div class="plugin-card-bottom">
					<div class="column-updated">
						<strong><?php esc_html_e( 'Version:', 'sparkling' ); ?></strong>
						<?php echo esc_html( $info->version ); ?>
					</div>
					<div class="column-compatibility">
						<?php if ( $is_active ) : ?>
							<span class="compatibility-compatible"><?php esc_html_e( 'Installed and active', 'sparkling' ); ?></span>
						<?php elseif ( 'activate' === $active['needs'] ) : ?>
							<span class="compatibility-untested"><?php esc_html_e( 'Installed, not active', 'sparkling' ); ?></span>
						<?php else : ?>
							<span class="compatibility-untested"><?php esc_html_e( 'Not installed', 'sparkling' ); ?></span>
						<?php endif; ?>
					</div>
				</div>
			</div>
			<?php
		}// End foreach().
		?>
	</div>
</div>
