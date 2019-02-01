<?php
/**
 * WP Multiple Authors Helpers.
 *
 * @since   0.1.0
 * @package WP_Multiple_Authors
 */

 /**
	* Get the author terms of the "current" post.
	*
	* @since 0.1.0
	*
	* @param int $post_id The ID of the post.
	* @return array WP_Term An array of author terms for authors associated with the post.
	*/
function wpma_post_authors( int $post_id, $taxonomy ) : array {

	 if ( 1 > $post_id ) {
		 trigger_error( 'Invalid post ID value: ' . $post_id );
		 return array();
	 }

	 $authors = get_the_terms( $post_id, $taxonomy );

	 if ( is_wp_error( $authors ) ) {
		 trigger_error( 'Invalid post ID value: ' . $post_id );
		 return array();
	 }

	 if ( ! is_array( $authors ) ) {
		 return array();
	 }

	 return $authors;
 }
