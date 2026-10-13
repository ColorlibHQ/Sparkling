<?php
/**
 * Recommended Plugins
 *
 * Uses core's own plugin-card markup (the same structure wp-admin renders on
 * Plugins > Add New) so the layout, grid, buttons and responsive behaviour all
 * come from core's plugin-install stylesheet, which Sparkling_Welcome enqueues
 * on admin_enqueue_scripts. The theme adds no CSS of its own for this tab.
 */

global $sparkling_recommended_plugins, $sparkling_welcome;

$sparkling_can_install  = current_user_can( 'install_plugins' );
$sparkling_can_activate = current_user_can( 'activate_plugins' );
?>

<div class="wp-list-table widefat plugin-install">
	<h2 class="screen-reader-text"><?php esc_html_e( 'Recommended plugins list', 'sparkling' ); ?></h2>

	<div id="the-list">
		<?php
		foreach ( array_keys( $sparkling_recommended_plugins ) as $sparkling_plugin ) {

			$sparkling_info = $sparkling_welcome->call_plugin_api( $sparkling_plugin );

			/*
			 * plugins_api() returns WP_Error when wordpress.org cannot be reached.
			 * Previously the properties below were read straight off it, so an
			 * offline site rendered a broken tab.
			 */
			if ( is_wp_error( $sparkling_info ) || empty( $sparkling_info->name ) ) {
				continue;
			}

			$sparkling_icon   = $sparkling_welcome->check_for_icon( $sparkling_info->icons );
			$sparkling_active = $sparkling_welcome->check_active( $sparkling_plugin );
			$sparkling_url    = $sparkling_welcome->create_action_link( $sparkling_active['needs'], $sparkling_plugin );

			$sparkling_is_active = ( 'install' !== $sparkling_active['needs'] && $sparkling_active['status'] );

			switch ( $sparkling_active['needs'] ) {
				case 'install':
					$sparkling_class = 'install-now button';
					$sparkling_label = __( 'Install Now', 'sparkling' );
					break;
				case 'activate':
					$sparkling_class = 'activate-now button button-primary';
					$sparkling_label = __( 'Activate', 'sparkling' );
					break;
				default:
					$sparkling_class = 'button';
					$sparkling_label = __( 'Deactivate', 'sparkling' );
					break;
			}

			$sparkling_details_url = add_query_arg(
				array(
					'tab'       => 'plugin-information',
					'plugin'    => $sparkling_plugin,
					'TB_iframe' => 'true',
					'width'     => 600,
					'height'    => 550,
				),
				self_admin_url( 'plugin-install.php' )
			);
			?>
			<div class="plugin-card plugin-card-<?php echo esc_attr( sanitize_html_class( $sparkling_plugin ) ); ?>">
				<div class="plugin-card-top">
					<div class="name column-name">
						<h3>
							<a href="<?php echo esc_url( $sparkling_details_url ); ?>" class="thickbox open-plugin-details-modal">
								<?php echo esc_html( $sparkling_info->name ); ?>
								<img src="<?php echo esc_url( $sparkling_icon ); ?>" class="plugin-icon" alt="" />
							</a>
						</h3>
					</div>

					<div class="action-links">
						<ul class="plugin-action-buttons">
							<li>
								<?php if ( $sparkling_is_active ) : ?>
									<button type="button" class="button button-disabled" disabled="disabled"><?php esc_html_e( 'Active', 'sparkling' ); ?></button>
								<?php elseif ( ( 'install' === $sparkling_active['needs'] && $sparkling_can_install ) || ( 'install' !== $sparkling_active['needs'] && $sparkling_can_activate ) ) : ?>
									<?php
									/*
									 * Only for users who can carry it out: an editor with
									 * edit_theme_options but not install_plugins got an Install
									 * button that ended on "Sorry, you are not allowed".
									 */
									?>
									<a data-slug="<?php echo esc_attr( $sparkling_plugin ); ?>"
										class="<?php echo esc_attr( $sparkling_class ); ?>"
										href="<?php echo esc_url( $sparkling_url ); ?>"><?php echo esc_html( $sparkling_label ); ?></a>
								<?php endif; ?>
							</li>
							<li>
								<a href="<?php echo esc_url( $sparkling_details_url ); ?>" class="thickbox open-plugin-details-modal">
									<?php esc_html_e( 'More Details', 'sparkling' ); ?>
								</a>
							</li>
						</ul>
					</div>

					<div class="desc column-description">
						<p><?php echo wp_kses_post( $sparkling_info->short_description ); ?></p>
						<p class="authors"><cite>
						<?php
							/* translators: %s: plugin author name */
							printf( esc_html__( 'By %s', 'sparkling' ), wp_kses_post( $sparkling_info->author ) );
						?>
						</cite></p>
					</div>
				</div>

				<div class="plugin-card-bottom">
					<div class="column-updated">
						<strong><?php esc_html_e( 'Version:', 'sparkling' ); ?></strong>
						<?php echo esc_html( $sparkling_info->version ); ?>
					</div>
					<div class="column-compatibility">
						<?php if ( $sparkling_is_active ) : ?>
							<span class="compatibility-compatible"><?php esc_html_e( 'Installed and active', 'sparkling' ); ?></span>
						<?php elseif ( 'activate' === $sparkling_active['needs'] ) : ?>
							<span class="compatibility-untested"><?php esc_html_e( 'Installed, not active', 'sparkling' ); ?></span>
						<?php else : ?>
							<span class="compatibility-untested"><?php esc_html_e( 'Not installed', 'sparkling' ); ?></span>
						<?php endif; ?>
					</div>
				</div>
			</div>
			<?php
		}
		?>
	</div>
</div>
