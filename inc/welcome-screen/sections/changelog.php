<?php
/**
 * About Sparkling: Changelog tab.
 *
 * @package sparkling
 */

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
		/*
		 * A plain read of the theme's own file. WP_Filesystem() is for writing
		 * during upgrades: on a host without direct file access it needs FTP
		 * credentials, so the tab showed "could not be read".
		 */
		$sparkling_changelog = file_get_contents( $sparkling_changelog_file ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents -- Local theme file.

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
