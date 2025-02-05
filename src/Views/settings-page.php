<?php
/**
 * Render settings page.
 *
 * @package WP_Beacon
 * @author  Verdant Studio
 * @since   1.0.0
 */

/**
 * Exit when accessed directly.
 */
if ( ! defined( 'ABSPATH' )) {
	exit;
}
?>

<div class="wpbcn:bg-white wpbcn:border-b wpbcn:flex wpbcn:full wpbcn:items-center wpbcn:justify-between wpbcn:p-4">
	<div class="wpbcn:flex wpbcn:items-center wpbcn:space-x-4">
		<img alt="" height="65" src="<?php echo esc_html( WP_BEACON_PLUGIN_URL ); ?>assets/img/logo.png" width="80" />
		<h1 class="wpbcn:text-2xl wpbcn:font-semibold">WP Beacon</h1>
	</div>
	<div class="wpbcn:bg-black wpbcn:px-2 wpbcn:py-1 wpbcn:rounded wpbcn:text-cyan-50">
		version <?php echo esc_html( WP_BEACON_VERSION ); ?>
	</div>
</div>
<div class="wpbcn:p-4">
	<div id="wp-beacon-settings"></div>
</div>
