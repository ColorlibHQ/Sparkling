<?php
/**
 * Changelog
 */

$sparkling = wp_get_theme( 'sparkling' );

?>
<div class="featured-section changelog">
	

	<?php
	WP_Filesystem();
	global $wp_filesystem;
	$sparkling_changelog       = $wp_filesystem->get_contents( get_template_directory() . '/changelog.txt' );
	$sparkling_changelog_lines = explode( PHP_EOL, $sparkling_changelog );
	foreach ( $sparkling_changelog_lines as $sparkling_changelog_line ) {
		if ( substr( $sparkling_changelog_line, 0, 3 ) === '###' ) {
			echo '<h4>' . substr( $sparkling_changelog_line, 3 ) . '</h4>';
		} else {
			echo $sparkling_changelog_line, '<br/>';
		}
	}

	echo '<hr />';


	?>

</div>
