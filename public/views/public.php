<?php
/**
 * @package   LikeDeslike
 * @author    Estevão Lucas <estevao.lucas@gmail.com>
 * @license   GPL-2.0+
 * @copyright 2014 Adjetiva
 */

$likeDeslike = LikeDeslike::get_instance();

function get_total_like ( $postID = null ) {
  global $likeDeslike;

  if ( empty( $postID ) ) {
    global $post;
    
    $postID = $post->ID;
  }

  return $likeDeslike->get_total_like( $postID );

}

function get_total_deslike ( $postID = null ) {
  global $likeDeslike;
  
  if ( empty( $postID ) ) {
    global $post;
    
    $postID = $post->ID;
  }

  return $likeDeslike->get_total_deslike( $postID );
}

function the_total_like ( $postID = null ) {
  
  echo get_total_like( $postID );

}

function the_total_deslike ( $postID = null ) {
  
  echo get_total_deslike( $postID );

}

?>
