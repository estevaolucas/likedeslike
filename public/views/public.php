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
  $token = $likeDeslike->token($postID, $type);
  
  return "<span class=\"likedeslike {$typeclass}\" data-token=\"{$token}\">{$likeDeslike->get_totalbytype( $postID, $type )}</span>";
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
  }

  $typeclass = $type == $likeDeslike->get_type_like() ? 'like' : 'deslike';
  $ajaxurl = site_url() . '/wp-admin/admin-ajax.php';
  $action = $likeDeslike->get_ajax_action();
  $token = $likeDeslike->token($postID, $type);

  return "<button type=\"button\" class=\"likedeslike {$typeclass}\" data-token=\"{$token}\" data-url=\"{$ajaxurl}\" data-user_id=\"1\" data-action=\"{$action}\">{$title}</button>";
}

function the_like_button ( $title = null,  $postID = null ) {
  if ( empty( $postID ) ) {
    $postID = get_the_ID();
  }

  global $likeDeslike;

  echo rating_button( $title, $postID, $likeDeslike->get_type_like() );
}

function the_deslike_button ( $title = null, $postID = null ) {
  if ( empty( $postID ) ) {
    $postID = get_the_ID();
  }

  global $likeDeslike;

  echo rating_button( $title, $postID, $likeDeslike->get_type_deslike() );
}

?>
