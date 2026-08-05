<?php
/**
 * Changelog
 */

$sparkling = wp_get_theme( 'sparkling' );

$sparkling_changelog_file = get_template_directory() . '/changelog.txt';

?>
<div class="featured-section changelog">


	<?php
	/*
	 * changelog.txt shipped missing from some releases. get_contents() then
	 * returned false, which was passed straight to explode() and echoed unescaped.
	 * Bail out with a notice instead, and escape every line that is printed.
	 */
	if ( ! file_exists( $sparkling_changelog_file ) || ! is_readable( $sparkling_changelog_file ) ) {
		echo '<p>' . esc_html__( 'The changelog file could not be found.', 'sparkling' ) . '</p>';
	} else {
		WP_Filesystem();
		global $wp_filesystem;

		$sparkling_changelog = $wp_filesystem ? $wp_filesystem->get_contents( $sparkling_changelog_file ) : false;

		if ( false === $sparkling_changelog || '' === $sparkling_changelog ) {
			echo '<p>' . esc_html__( 'The changelog file could not be read.', 'sparkling' ) . '</p>';
		} else {
			$sparkling_changelog_lines = preg_split( '/\R/', $sparkling_changelog );

			foreach ( $sparkling_changelog_lines as $sparkling_changelog_line ) {
				if ( substr( $sparkling_changelog_line, 0, 3 ) === '###' ) {
					echo '<h4>' . esc_html( substr( $sparkling_changelog_line, 3 ) ) . '</h4>';
				} else {
					echo esc_html( $sparkling_changelog_line ), '<br/>';
				}
			}
		}

		echo '<hr />';
	}
	?>

</div>
