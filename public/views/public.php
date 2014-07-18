<?php
/**
 * @package   LikeDeslike
 * @author    Estevão Lucas <estevao.lucas@gmail.com>
 * @license   GPL-2.0+
 * @copyright 2014 Adjetiva
 */

$likeDeslike = LikeDeslike::get_instance();

function get_totalbytype ( $postID = null, $type ) {
  global $likeDeslike;
  
  if ( empty( $postID ) ) {
    global $post;
    
    $postID = $post->ID;
  }

  $typeclass = $type == $likeDeslike->get_type_like() ? 'like' : 'deslike';
  
  return '<span class="likedeslike ' . $typeclass . '" data-post_id="'. $postID . '" data-type="' .  $type . '">' . $likeDeslike->get_totalbytype( $postID, $type ) . '</span>';
}

function the_total_like ( $postID = null ) {
  global $likeDeslike;
  echo get_totalbytype( $postID, $likeDeslike->get_type_like() );
}

function the_total_deslike ( $postID = null ) {
  global $likeDeslike;
  echo get_totalbytype( $postID, $likeDeslike->get_type_deslike() );
}

function rating_button ( $title, $postID = null, $type ) {
  global $likeDeslike;

  if ( empty( $title ) ) {
    $title = __($type == $likeDeslike->get_type_like() ? 'Like' : 'Deslike', 'likedeslike');
  } else {
    $title = __($title, 'likedeslike');
  }

  $typeclass = $type == $likeDeslike->get_type_like() ? 'like' : 'deslike';
  $ajaxurl = site_url() . '/wp-admin/admin-ajax.php';
  $action = 'likedeslike_process_rating';

  ?>
  <button type="button" class="likedeslike <?php echo $typeclass; ?>" data-post_id="<?php echo trim($postID); ?>" data-type="<?php echo $type; ?>" data-url="<?php echo $ajaxurl; ?>" data-user_id="1" data-action="<?php echo $action; ?>"> <?php echo $title ?></button>
  <?php
}

function the_like_button ( $title = null,  $postID = null ) {
  if ( empty( $postID ) ) {
    $postID = get_the_ID();
  }

  global $likeDeslike;

  rating_button( $title, $postID, $likeDeslike->get_type_like() );
}

function the_deslike_button ( $title = null, $postID = null ) {
  if ( empty( $postID ) ) {
    $postID = get_the_ID();
  }

  global $likeDeslike;

  rating_button( $title, $postID, $likeDeslike->get_type_deslike() );
}

?>
