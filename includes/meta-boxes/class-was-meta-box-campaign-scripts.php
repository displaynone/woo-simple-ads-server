<?php
/**
 * WooCommerce Simple Ads Campaigns Scripts meta box
 *
 * Display a list of campaign scripts
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * WooAds_Meta_Box_Campaign_Scripts Class.
 */
class WooAds_Meta_Box_Campaign_Scripts {

  function WooAds_Meta_Box_Campaign_Scripts() {
    add_action( 'add_meta_boxes', array( $this, 'add_campaings_products_meta_boxes' ), 40 );

  }

	/**
	 * Output the metabox.
	 *
	 * @param WP_Post $post
	 */
	public function output( $post ) {
		global $wpdb;
		wp_enqueue_script('was_metabox_campaign_scripts', plugins_url('../../js/admin/meta_box_scripts.js', __FILE__));
		?>
		<div id="message_scripts" class="hidden"><p><?php _e('You must update the campaing to refresh the list of scripts to share with other webs.', 'displaynone'); ?></p></div>
		<?php
		$sizes = WooAds_Banner::get_sizes();
		$banners = $wpdb->get_results($wpdb->prepare("SELECT meta_key FROM {$wpdb->postmeta} WHERE meta_key like %s AND meta_value = %d GROUP BY meta_key", '_campaign_banner_%', $post->ID));
		foreach($banners as $banner) {
			$size = str_replace('_campaign_banner_', '', $banner->meta_key);
			echo '<h3>'.$sizes[$size].'</h3>';
			echo '<div class="script_container"><textarea>&lt;script type="text/javascript" src="'.home_url('was_campaign/'.$post->ID.'/'.$size.'.js').'" /&gt;</textarea></div>';
		}
	}

  /**
   * Add meta boxes to WooCommerce products
   */
  function add_campaings_products_meta_boxes() {
    add_meta_box( 'woocommerce-campaign-scripts', __( 'Campaign scripts', 'displaynone' ), 'WooAds_Meta_Box_Campaign_Scripts::output', 'was_campaign', 'normal', 'low' );
  }

}

new WooAds_Meta_Box_Campaign_Scripts();
