<?php

/**
 * WooCommerce Simple Ads Server Campaigns Object
 */
class WooAds_Campaigns {

  function WooAds_Campaigns() {
    $this->init();
  }

  function init() {
    $this->actions();
  }

  function actions() {
    add_action('init', array($this, 'create_custom_post_type'));
    add_action('init', array($this, 'add_script_rules' ));
    add_action('wp', array($this, 'show_script' ));
    add_filter('query_vars', array($this,  'add_query_vars_filter' ));
  }

  /**
   * Create custom post type Campaing
   */
  function create_custom_post_type() {
    $labels = array(
  		'name'                => __('Campaigns', 'displaynone'),
  		'singular_name'       => __('Campaign', 'displaynone'),
  		'menu_name'           => __('Campaigns', 'displaynone'),
  		'parent_item_colon'   => __('Campaign Item:', 'displaynone'),
  		'all_items'           => __('All campaigns', 'displaynone'),
  		'view_item'           => __('View campaign', 'displaynone'),
  		'add_new_item'        => __('Add new campaign', 'displaynone'),
  		'add_new'             => __('Add campaign', 'displaynone'),
  		'edit_item'           => __('Edit campaign', 'displaynone'),
  		'update_item'         => __('Update campaign', 'displaynone'),
  		'search_items'        => __('Search campaign', 'displaynone'),
  		'not_found'           => __('No campaigns were found', 'displaynone'),
  		'not_found_in_trash'  => __('No campaigns were found in the trash', 'displaynone'),
  	);
  	$args = array(
  		'label'               => 'campaign',
  		'description'         => __('Campaigns', 'displaynone'),
  		'labels'              => $labels,
  		'supports'            => array( 'title', 'excerpt' ),
  		'hierarchical'        => false,
  		'public'              => false,
  		'show_ui'             => true,
  		'show_in_menu'        => true,
  		'show_in_nav_menus'   => true,
  		'show_in_admin_bar'   => true,
  		'menu_position'       => 5,
  		'can_export'          => true,
  		'has_archive'         => false,
  		'exclude_from_search' => true,
  		'publicly_queryable'  => false,
  		'capability_type'     => 'page',
  	);
  	register_post_type( 'was_campaign', $args );
  }

  /**
   * Add scripts rules
   */
  function add_script_rules() {
    add_rewrite_rule(
      "was_campaign/([^/]+)/([^/]+).js?",
      'index.php?was_campaign=$matches[1]&was_banner=$matches[2]&was_type=javascript',
      "top"
    );
  }

  /**
   * Add query vars
   */
  function add_query_vars_filter( $vars ){
    $vars[] = "was_campaign";
    $vars[] = "was_banner";
    $vars[] = "was_type";
    return $vars;
  }

  /**
   * Show script content
   */
  function show_script() {
    $campaign = get_query_var('was_campaign');
    if ($campaign) {
      $campaign = get_post($campaign);
      if ($campaign && $campaign->post_type == 'was_campaign') {
        global $wpdb;
        $size = get_query_var('was_banner');
        $banner_id = $wpdb->get_var($wpdb->prepare("SELECT post_id as ID FROM {$wpdb->postmeta} WHERE meta_key = %s AND meta_value = %d ORDER BY rand() LIMIT 1", '_campaign_banner_'.$size, $campaign->ID));
        if ($banner_id) {
          $image_src = wp_get_attachment_image( $banner_id, 'full' );
          if ($image_src) {
            $product_id = $wpdb->get_var($wpdb->prepare("SELECT meta_value as ID FROM {$wpdb->postmeta} WHERE meta_key = '_was_banner' AND post_id = %d", $banner_id));
            if ($product_id) {
              header('Content-type: text/javascript');
              ?>
              document.write('<a href="<?php echo get_permalink($product_id); ?>" target="_blank"><?php echo $image_src; ?></a>');
              <?php
              exit();
            }
          }
        }
      }
    }
  }
}
