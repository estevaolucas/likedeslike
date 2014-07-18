<?php
/**
 * The WordPress Plugin Boilerplate.
 *
 * A foundation off of which to build well-documented WordPress plugins that
 * also follow WordPress Coding Standards and PHP best practices.
 *
 * @package   LikeDeslike
 * @author    Estevão Lucas <estevao.lucas@gmail.com>
 * @license   GPL-2.0+
 * @link      http://github.com/estevaoluca
 * @copyright 2014 Adjetiva
 *
 * @wordpress-plugin
 * Plugin Name:       Like Deslike
 * Description:       A simple way to like or deslike a post using Facebook Connect.
 * Version:           0.0.1
 * Author:            Estevão Lucas
 * Author URI:        http://github.com/estevaolucas
 * Text Domain:       LikeDeslike
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Domain Path:       /languages
 * GitHub Plugin URI: https://github.com/estevaolucas/LikeDeslikeWordPressPlugin
 * WordPress-Plugin-Boilerplate: v2.6.1
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
  die;
}

/*----------------------------------------------------------------------------*
 * Public-Facing Functionality
 *----------------------------------------------------------------------------*/

require_once( plugin_dir_path( __FILE__ ) . 'public/class-likedeslike.php' );
require_once( plugin_dir_path( __FILE__ ) . 'public/views/public.php' );

/*
 * Register hooks that are fired when the plugin is activated or deactivated.
 * When the plugin is deleted, the uninstall.php file is loaded.
 */
register_activation_hook( __FILE__, array( 'LikeDeslike', 'activate' ) );
register_deactivation_hook( __FILE__, array( 'LikeDeslike', 'deactivate' ) );

add_action( 'plugins_loaded', array( 'LikeDeslike', 'get_instance' ) );