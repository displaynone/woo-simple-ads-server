<?php
/**
 * WooCommerce Product Banners
 *
 * Display the product banners meta box. Clone of WC_Meta_Box_product_banners class
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 */
class WooAds_Banner {
  public static $sizes = array();

  function WooAds_Banner() {
    $this->init();
  }

  private function init() {
    $this->set_sizes();
  }

  /**
   * Sets standard banner sizes
   */
  private function set_sizes() {
    $sizes['468x60'] = __('Full Banner (468x60)', 'displaynone');
    $sizes['728x90'] = __('Leaderboard (728x90)', 'displaynone');
    $sizes['336x280'] = __('Square (336x280)', 'displaynone');
    $sizes['300x250'] = __('Square (300x250)', 'displaynone');
    $sizes['250x250'] = __('Square (250x250)', 'displaynone');
    $sizes['160x600'] = __('Skyscraper (160x600)', 'displaynone');
    $sizes['120x600'] = __('Skyscraper (120x600)', 'displaynone');
    $sizes['120x240'] = __('Small Skyscraper (120x240)', 'displaynone');
    $sizes['240x400'] = __('Fat Skyscraper (240x400)', 'displaynone');
    $sizes['234x60'] = __('Half Banner (234x60)', 'displaynone');
    $sizes['180x150'] = __('Rectangle (180x150)', 'displaynone');
    $sizes['125x125'] = __('Square Button (125x125)', 'displaynone');
    $sizes['120x90'] = __('Button (120x90)', 'displaynone');
    $sizes['120x60'] = __('Button (120x60)', 'displaynone');
    $sizes['88x31'] = __('Button (88x31)', 'displaynone');
    $sizes['x'] = __('Not standard', 'displaynone');

    self::$sizes = apply_filters('was_set_sizes_banners', $sizes);
  }

  /**
   * Get banner size using width and height
   *
   * @param int $width
   * @param int $height
   * @return string Size index
   */
  public static function get_size_by_meta($width, $height) {
    if (empty(self::$sizes)) self::set_sizes();
    return isset(self::$sizes[$width.'x'.$height])? $width.'x'.$height : 'x';
  }

	/**
   * Get banner size
   *
   * @param int $width
   * @param int $height
   * @return string Size index
   */
  public static function get_size($idx) {
    if (empty(self::$sizes)) self::set_sizes();
    return isset(self::$sizes[$idx])? self::$sizes[$idx] : self::$sizes['x'];
  }

	/**
   * Get banner sizes
   *
   * @return array Sizes index
   */
  public static function get_sizes() {
    if (empty(self::$sizes)) self::set_sizes();
    return self::$sizes;
  }


}
