<?php
/**
 * WooCommerce Simple Ads Product Banners
 *
 * Display the product banners meta box. Clone of WC_Meta_Box_product_banners class
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * WooAds_Meta_Box_Product_Banners Class.
 */
class WooAds_Meta_Box_Product_Banners {

  function WooAds_Meta_Box_Product_Banners() {
    add_action( 'save_post', array( $this, 'save_meta_boxes' ), 1, 2 );
    add_action( 'add_meta_boxes', array( $this, 'add_woocommerce_products_meta_boxes' ), 40 );

  }

	/**
	 * Output the metabox.
	 *
	 * @param WP_Post $post
	 */
	public function output( $post ) {
    wp_enqueue_script('was_metabox_product_banners', plugins_url('../../js/admin/meta_box_banner.js', __FILE__));
		wp_localize_script('was_metabox_product_banners', 'banners', array('sizes'=>WooAds_Banner::get_sizes()));
		?>
		<div id="product_banners_container">
			<div class="product_banners">
				<?php
					$product_image_gallery = get_post_meta( $post->ID, '_product_image_banner', true );
					$attachments         = array_filter( explode( ',', $product_image_gallery ) );
					$update_meta         = false;
					$updated_gallery_ids = array();

					$grouped = array();

					if ( ! empty( $attachments ) ) {
						foreach ( $attachments as $attachment_id ) {
							$attachment = wp_get_attachment_image( $attachment_id, 'full' );
							$attach_meta = wp_get_attachment_metadata($attachment_id);
							$size = WooAds_Banner::get_size_by_meta($attach_meta['width'], $attach_meta['height']);
							if (!isset($grouped[$size])) $grouped[$size] = array();
							$grouped[$size][$attachment_id] = $attachment;
						}
						foreach($grouped as $size => $group) {
							echo '<h3>'.WooAds_Banner::get_size($size).'</h3>';
							echo '<div class="product_banners_group_'.$size.'">';
							foreach($group as $attachment_id => $attachment) {
								echo '<div class="image" data-attachment_id="' . esc_attr( $attachment_id ) . '">
									' . $attachment . '
									<ul class="actions">
										<li><a href="#" class="delete tips" data-tip="' . esc_attr__( 'Delete image', 'displaynone' ) . '"></a></li>
									</ul>
								</div>';
							}
							echo '</div>';
						}
						// need to update product meta to set new gallery ids
						if ( $update_meta ) {
							update_post_meta( $post->ID, '_product_image_banner', implode( ',', $updated_gallery_ids ) );
						}
					}
				?>
			</div>

			<input type="hidden" id="product_image_banner" name="product_image_banner" value="<?php echo esc_attr( $product_image_gallery ); ?>" />

		</div>
		<p class="add_product_banners hide-if-no-js">
			<a href="#" data-choose="<?php esc_attr_e( 'Add ads banner', 'displaynone' ); ?>" data-update="<?php esc_attr_e( 'Add to gallery', 'displaynone' ); ?>" data-delete="<?php esc_attr_e( 'Delete image', 'displaynone' ); ?>" data-text="<?php esc_attr_e( 'Delete', 'displaynone' ); ?>"><?php _e( 'Add ads product banners', 'woocommerce' ); ?></a>
		</p>
		<?php
	}

  /**
   * Add meta boxes to WooCommerce products
   */
  function add_woocommerce_products_meta_boxes() {
    add_meta_box( 'woocommerce-product-banners', __( 'Product ads banners', 'displaynone' ), 'WooAds_Meta_Box_product_banners::output', 'product', 'normal', 'low' );
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
		if ( empty( $_POST['woocommerce_meta_nonce'] ) || ! wp_verify_nonce( $_POST['woocommerce_meta_nonce'], 'woocommerce_save_data' ) ) {
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

    $attachment_ids = isset( $_POST['product_image_banner'] ) ? array_filter( explode( ',', wc_clean( $_POST['product_image_banner'] ) ) ) : array();
		global $wpdb;
		$wpdb->query($wpdb->prepare("delete from {$wpdb->postmeta} where post_id = %s and meta_key = '_was_banner'", $post_id));
		$wpdb->query($wpdb->prepare("delete from {$wpdb->postmeta} where post_id = %s and meta_key = '_was_banner_size'", $post_id));
    foreach($attachment_ids as $id) {
			update_post_meta($id, '_was_banner', $post_id);
			$attach_meta = wp_get_attachment_metadata($id);
			update_post_meta($id, '_was_banner_size', array($attach_meta['width'], $attach_meta['height']));
			update_post_meta($id, '_was_banner_size_type', WooAds_Banner::get_size_by_meta($attach_meta['width'], $attach_meta['height']));
    }
		update_post_meta( $post_id, '_product_image_banner', implode( ',', $attachment_ids ) );
  }

}

new WooAds_Meta_Box_Product_Banners();
