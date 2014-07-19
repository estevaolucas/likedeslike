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
  const TABLE_RECORDS = "likedeslike_table_records";

  protected $ajax_action = 'likedeslike_process_rating';
	protected $plugin_slug = 'likedeslike';
  protected $encrypt_method = "AES-256-CBC";
  protected $secret_key = '2a49$3dfj4kdb2m143&*';
  protected $secret_iv = '495D#1!#%204d#0dlt5';

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

  private static function get_table_records_name() {
    global $wpdb;

    return $wpdb->prefix . self::TABLE_RECORDS;
  }

	private static function single_activate() {
    global $wpdb;
    global $charset_collate;

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

    $table = self::get_table_records_name();

    $sql_create_table = "CREATE TABLE {$table} (
      ID bigint(20) unsigned NOT NULL auto_increment,
      user_id bigint(20) unsigned NOT NULL default '0',
      post_id bigint(20) unsigned NOT NULL default '0',
      type bigint(20) unsigned NOT NULL default '0',
      created_at datetime NOT NULL default NOW(),
      PRIMARY KEY  (ID),
      UNIQUE KEY uniquelike (user_id,post_id),
      KEY abc (user_id)
      ) $charset_collate; ";

    dbDelta($sql_create_table);
	}

	private static function single_deactivate() {
		// @TODO: Define deactivation functionality
	}

	public function enqueue_styles() {
		wp_enqueue_style( $this->plugin_slug . '-plugin-styles', plugins_url( 'assets/css/public.css', __FILE__ ), array(), self::VERSION );
	}

	public function enqueue_scripts() {
		wp_enqueue_script( $this->plugin_slug . '-plugin-script', plugins_url( 'assets/js/public.js', __FILE__ ), array( 'jquery' ), self::VERSION );
	}

  public function get_totalbytype( $postID, $type ) {
    global $wpdb;

    $table = self::get_table_records_name();

    return $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM $table WHERE post_id = %d && type = %d;", $postID, $type ) ); 
  }

  public function get_total_like( $postID ) {
    return $this->get_totalbytype( $postID, self::TYPE_LIKE );
  }

  public function get_total_deslike( $postID ) {
    return $this->get_totalbytype( $postID, self::TYPE_DESLIKE );
  }

  public static function process_rating () {
    $likedeslike = self::get_instance();

    // TODO user id authentication;
    $likedeslike->post_rating($_POST);

    die();
  }

  private function post_rating( $data ) {
    // TODO validation
    header('Content-Type: application/json');

    global $wpdb;

    // decryptation
    list($postID, $type) = explode(';', $this->encrypt_decrypt('decrypt', $data['token']));
    $data['post_id'] = $postID;
    $data['type'] = $type;

    $column_formats = $this->get_table_columns();
    $data = array_intersect_key( $data, $column_formats );

    //Reorder $column_formats to match the order of columns given in $data
    $data_keys = array_keys( $data );
    $column_formats = array_merge( array_flip($data_keys ), $column_formats );

    $return = array( 'success' => false );

    $wpdb->hide_errors();
    
    if ( $wpdb->insert( self::get_table_records_name(), $data, $column_formats ) ) {
      $return['success'] = true;
      $return['count'] = $this->get_totalbytype( $data['post_id'], $data['type'] );
    } else {
      if ( $wpdb->delete( self::get_table_records_name(), $data )  ) {
        $return['success'] = true;
        $return['count'] = $this->get_totalbytype( $data['post_id'], $data['type'] );
      }
    }

    echo json_encode( $return );
  }

  private function get_table_columns() {
    return array(
        'ID'      => '%d',
        'user_id' => '%d',
        'post_id' =>'%d',
        'type'    =>'%d'
    );
  }

  public function get_type_like () {
    return self::TYPE_LIKE;
  }

  public function get_type_deslike () {
    return self::TYPE_DESLIKE;
  }

  public function token( $postID, $type ) {
    return $this->encrypt_decrypt('encrypt', $postID . ';' . $type);
  }

  public function encrypt_decrypt( $action, $string ) {
    $output = false;

    $key = hash( 'sha256', $this->secret_key );
    
    // iv - encrypt method AES-256-CBC expects 16 bytes - else you will get a warning
    $iv = substr(hash('sha256', $this->secret_iv), 0, 16);

    if( $action == 'encrypt' ) {
      $output = openssl_encrypt($string, $this->encrypt_method, $key, 0, $iv);
      $output = base64_encode($output);
    } else if( $action == 'decrypt' ) {
      $output = openssl_decrypt(base64_decode($string), $this->encrypt_method, $key, 0, $iv);
    }

    return $output;
  }

  public function get_ajax_action() {
    return $this->ajax_action;
  }
}
