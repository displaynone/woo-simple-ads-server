<?php
/**
 * WooCommerce Simple Ads Campaigns Products meta box
 *
 * Display a list of products in a campaign
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * WooAds_Meta_Box_Campaign_Products Class.
 */
class WooAds_Meta_Box_Campaign_Products {

  function WooAds_Meta_Box_Campaign_Products() {
    add_action( 'save_post', array( $this, 'save_meta_boxes' ), 1, 2 );
    add_action( 'add_meta_boxes', array( $this, 'add_campaings_products_meta_boxes' ), 40 );

  }

	/**
	 * Output the metabox.
	 *
	 * @param WP_Post $post
	 */
	public function output( $post ) {
		global $wpdb;
    wp_enqueue_script('was_metabox_campaign_products', plugins_url('../../js/admin/meta_box_products.js', __FILE__));
		wp_localize_script('was_metabox_campaign_products', 'products', array('sizes'=>WooAds_Banner::get_sizes()));
		$campaign_products = get_post_meta($post->ID, '_campaign_products', true);
		?>
		<div id="campaign_products_container">
			<h3><?php _e('Campaign products', 'displaynone'); ?></h3>
			<p><?php _e('Select which products you want to show in this campaing', 'displaynone'); ?></p>
			<p><select id="campaign_products_list">
				<option value=""><?php _e('Select product', 'displaynone'); ?></option>
			<?php
				$products = $wpdb->get_results("select products.* from {$wpdb->posts} products, {$wpdb->postmeta} banner where products.post_type = 'product' and banner.meta_value = products.ID and banner.meta_key = '_was_banner' group by products.ID order by post_title");
				$products_list = '';
				$products_ids = explode(',', $campaign_products);
				foreach($products as $product) {
					$link = get_permalink($product->ID);
					$thumb = get_the_post_thumbnail_url($product->ID, 'thumbnail');
					$banners = $wpdb->get_results($wpdb->prepare("select banner.meta_value post_id, banner.post_id attachment_id, sizes.meta_value sizes from {$wpdb->postmeta} banner, {$wpdb->postmeta} sizes where banner.meta_value = %s and banner.meta_key = '_was_banner' and banner.post_id = sizes.post_id and sizes.meta_key = '_was_banner_size'", $product->ID));
					$sizes = array();
					foreach($banners as $banner) {
						$s = unserialize($banner->sizes);
						$s = WooAds_Banner::get_size_by_meta($s[0], $s[1]);
						if (empty($sizes[$s])) $sizes[$s] = 0;
						$sizes[$s]++;
					}
					?>
					<option value="<?php echo $product->ID; ?>" data-thumbnail="<?php echo $thumb; ?>" data-sizes='<?php echo json_encode($sizes); ?>' data-link="<?php echo $link; ?>"><?php echo $product->post_title; ?></option>
					<?php
					if (in_array($product->ID, $products_ids)) {
						$count = 0;
			      $help = '<div class="banners_help">';
			      foreach($sizes as $banner=>$n) {
			        $count += $n;
			        $help .= '<p>'.$n .' '. WooAds_Banner::get_size($banner).'</p>';
			      }
			      if ($count == 0) $help .= '<p>'.__('No banners', 'displaynone').'</p>';
			      $help .= '</div>';
			      $help = ' <div class="product_tooltip"> '.$count.__('banners', 'displaynone').' '.$help.'</div>';
			      $products_list .= '<div class="product" id="product-'.$product->ID.'" data-product-id="'.$product->ID.'" ><a href="'. $link. '" target="_blank"><img src="'.$thumb.'" />'.$product->post_title.'</a>'.$help.' <button class="button delete-button">'.__('Delete', 'displaynone').'</button></div>';
					}
				}
			?>
		</select> <button class="button-primary" id="campaign_products_list_add"><?php _e('Add product', 'displaynone'); ?></button>
			</p>
			<div class="campaign_products">
				<?php echo $products_list; ?>
			</div>

			<input type="hidden" id="campaign_products" name="campaign_products" value="<?php echo esc_attr( $campaign_products ); ?>" />
			<input type="hidden" id="campaign_products_nonce" name="campaign_products_nonce" value="<?php echo wp_create_nonce( 'campaign_products' ); ?>" />

		</div>
		<?php
	}

  /**
   * Add meta boxes to WooCommerce products
   */
  function add_campaings_products_meta_boxes() {
    add_meta_box( 'woocommerce-campaign-products', __( 'Products', 'displaynone' ), 'WooAds_Meta_Box_Campaign_Products::output', 'was_campaign', 'normal', 'low' );
  }

  /**
	 * Check if we're saving, the trigger an action based on the post type.
	 *
	 * @param  int $post_id
	 * @param  object $post
	 */
	function save_meta_boxes( $post_id, $post ) {

		// $post_id and $post are required
		if ( empty( $post_id ) || empty( $post ) ) {
			return;
		}

		// Dont' save meta boxes for revisions or autosaves
		if ( defined( 'DOING_AUTOSAVE' ) || is_int( wp_is_post_revision( $post ) ) || is_int( wp_is_post_autosave( $post ) ) ) {
			return;
		}

		// Check the nonce
		if ( empty( $_POST['campaign_products_nonce'] ) || ! wp_verify_nonce( $_POST['campaign_products_nonce'], 'campaign_products' ) ) {
			return;
		}

		// Check the post being saved == the $post_id to prevent triggering this call for other save_post events
		if ( empty( $_POST['post_ID'] ) || $_POST['post_ID'] != $post_id ) {
			return;
		}

		// Check user has permission to edit
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

    $products_ids = isset( $_POST['campaign_products'] ) ? array_filter( explode( ',', wc_clean( $_POST['campaign_products'] ) ) ) : array();
		global $wpdb;
		$in = implode(", ", $products_ids);
		$wpdb->query($wpdb->prepare("delete from {$wpdb->postmeta} where meta_key = '_campaign_banner' and meta_value = %d", $post_id));
//		$wpdb->query($wpdb->prepare("delete from {$wpdb->postmeta} where meta_key = '_campaign_banner' and meta_value = %d", $post_id));
//		$wpdb->query($wpdb->prepare("delete from {$wpdb->postmeta} where meta_key = '_campaign_banner' and meta_value = %d", $post_id));

		// Banners of a campaign
		$wpdb->query($wpdb->prepare("insert into wp_postmeta (post_id, meta_key, meta_value) select images.ID, '_campaign_banner', %d from wp_posts images, wp_postmeta meta where images.post_type = 'attachment' and meta.post_id = images.ID and meta.meta_key = '_was_banner' and meta_value in ({$in})", $post_id));

		// Banners of a campaign by size
		$wpdb->query($wpdb->prepare("insert into wp_postmeta (post_id, meta_key, meta_value) select images.ID, concat('_campaign_banner_', stypes.meta_value), %d from wp_posts images, wp_postmeta meta, wp_postmeta stypes where images.post_type = 'attachment' and meta.post_id = images.ID and meta.meta_key = '_was_banner' and meta.meta_value in ({$in}) and stypes.meta_key = '_was_banner_size_type' and meta.post_id = stypes.post_id", $post_id));

		update_post_meta( $post_id, '_campaign_products', implode( ',', $products_ids ) );
  }

}

new WooAds_Meta_Box_Campaign_Products();
