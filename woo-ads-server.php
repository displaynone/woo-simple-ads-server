<?php
/*
Plugin Name: WooCommerce Simple Ads Server
Description: Create ads campaigns for selling your own products in other webs
Version: 0.1
Author: Luis Sacristán
Author URI: http://sentidoweb.com
*/

/**
 * WooCommerce Simple Ads Server
 */
class WooAds_Server {
  public $campaigns;

  function WooAds_Server() {
    $this->includes();
    $this->init();
    $this->actions();
  }


  /**
   *  Init functions
   */
  function init() {
    $campaigns = new WooAds_Campaigns();
  }

  /**
   * Files imports
   */
  function includes() {
    include dirname(__FILE__).'/includes/helpers/class-was-banner.php';
    include dirname(__FILE__).'/includes/meta-boxes/class-was-meta-box-campaign-products.php';
    include dirname(__FILE__).'/includes/meta-boxes/class-was-meta-box-campaign-scripts.php';
    include dirname(__FILE__).'/includes/meta-boxes/class-was-meta-box-product-banners.php';
    include dirname(__FILE__).'/includes/post-types/class-was-campaign.php';
  }

  /**
   * Actions and filters
   */
  function actions() {
    add_action( 'admin_enqueue_scripts', array($this, 'admin_scripts') );
  }

  /**
   * Shows main admin page
   */
  function admin_main_page() {
    echo 'Prueba';
  }

  /**
   * Shows admin CSS
   */
  function admin_scripts() {
    wp_enqueue_style( 'was_admin', plugins_url('css/admin.css', __FILE__) );
  }


}

$wooads = new WooAds_Server();
