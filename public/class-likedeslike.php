<?php
/**
 * Like Deslike.
 *
 * @package   LikeDeslike
 * @author    Estevão Lucas <estevao.lucas@gmail.com>
 * @license   GPL-2.0+
 * @link      http://example.com
 * @copyright 2014 Your Name or Company Name
 */

class LikeDeslike {

	const VERSION = '1.0.0';

  const TYPE_LIKE = 0;
  const TYPE_DESLIKE = 1;

	protected $plugin_slug = 'likedeslike';

	protected static $instance = null;

	private function __construct() {

		// Activate plugin when new blog is added
		add_action( 'wpmu_new_blog', array( $this, 'activate_new_site' ) );

		// Load public-facing style sheet and JavaScript.
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
	}

	public function get_plugin_slug() {
		return $this->plugin_slug;
	}

	public static function get_instance() {

		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	public static function activate( $network_wide ) {

		if ( function_exists( 'is_multisite' ) && is_multisite() ) {

			if ( $network_wide  ) {

				// Get all blog ids
				$blog_ids = self::get_blog_ids();

				foreach ( $blog_ids as $blog_id ) {

					switch_to_blog( $blog_id );
					self::single_activate();

					restore_current_blog();
				}

			} else {
				self::single_activate();
			}

		} else {
			self::single_activate();
		}

	}

	public static function deactivate( $network_wide ) {

		if ( function_exists( 'is_multisite' ) && is_multisite() ) {

			if ( $network_wide ) {

				// Get all blog ids
				$blog_ids = self::get_blog_ids();

				foreach ( $blog_ids as $blog_id ) {

					switch_to_blog( $blog_id );
					self::single_deactivate();

					restore_current_blog();

				}

			} else {
				self::single_deactivate();
			}

		} else {
			self::single_deactivate();
		}

	}

	public function activate_new_site( $blog_id ) {

		if ( 1 !== did_action( 'wpmu_new_blog' ) ) {
			return;
		}

		switch_to_blog( $blog_id );
		self::single_activate();
		restore_current_blog();

	}

	private static function get_blog_ids() {

		global $wpdb;

		// get an array of blog ids
		$sql = "SELECT blog_id FROM $wpdb->blogs
			WHERE archived = '0' AND spam = '0'
			AND deleted = '0'";

		return $wpdb->get_col( $sql );

	}

	private static function single_activate() {
    
    global $wpdb;
    global $charset_collate;

    $wpdb->likedelike_posts = "{$wpdb->prefix}likedelike_posts";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

    $sql_create_table = "CREATE TABLE {$wpdb->likedelike_posts} (
      ID bigint(20) unsigned NOT NULL auto_increment,
      user_id bigint(20) unsigned NOT NULL default '0',
      post_id bigint(20) unsigned NOT NULL default '0',
      type bigint(20) unsigned NOT NULL default '0',
      created_at datetime NOT NULL default NOW(),
      PRIMARY KEY  (ID),
      KEY abc (user_id)
      ) $charset_collate; ";

    dbDelta($sql_create_table);
	}

	private static function single_deactivate() {
		// @TODO: Define deactivation functionality here
	}

	public function enqueue_styles() {
		wp_enqueue_style( $this->plugin_slug . '-plugin-styles', plugins_url( 'assets/css/public.css', __FILE__ ), array(), self::VERSION );
	}

	public function enqueue_scripts() {
		wp_enqueue_script( $this->plugin_slug . '-plugin-script', plugins_url( 'assets/js/public.js', __FILE__ ), array( 'jquery' ), self::VERSION );
	}

  public function get_totalbytype( $postID, $type ) {

    global $wpdb;
    
    $wpdb->likedelike_posts = "{$wpdb->prefix}likedelike_posts";

    return $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM $wpdb->likedelike_posts WHERE post_id = %d && type = %d;", $postID, $type ) ); 

  }

  public function get_total_like( $postID ) {

    return $this->get_totalbytype( $postID, self::TYPE_LIKE );

  }

  public function get_total_deslike( $postID ) {

    return $this->get_totalbytype( $postID, self::TYPE_DESLIKE );

  }
}
