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

}
