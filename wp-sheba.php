<?php

/**
 *
 * @link              https://saeedsoltoon.github.io/
 * @since             1.0.0
 * @package           Wp_Sheba
 *
 * @wordpress-plugin
 * Plugin Name:       WP Sheba
 * Plugin URI:        https://adak.shop
 * Description:       Custom Sheba woocommerce gateway.
 * Version:           1.0.0
 * Author:            Hossein Ghazi
 * Author URI:        https://saeedsoltoon.github.io/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       wp-sheba
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
	die;
}

// If logs dir not exists create it.
if (!is_dir(ABSPATH . 'logs')) {
	mkdir(ABSPATH . 'logs', 0755, true);
}

add_action( 'woocommerce_loaded', function () {

	require_once( 'includes/class-wp-sheba-gateway.php' );
    run_wp_sheba();

} );


function run_wp_sheba() {
    file_put_contents(ABSPATH . 'logs/ghazi.log', 'run_wp_sheba fired' .PHP_EOL, FILE_APPEND);
    new Wp_Sheba_Gateway();
}

