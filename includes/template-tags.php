<?php
/**
 * WP Multiple Authors Template Tags.
 *
 * @since   0.1.0
 * @package WP_Multiple_Authors
 */

/**
 * Template tag to display post authors
 *
 * @since 0.1.0
 *
 * @uses get_the_term_list()
 *
 * @param  string $before
 * @param  string $sep
 * @param  string $after
 * @return string terms
 */
function wpma_the_author( $before = null, $sep = ', ', $after = null ) {
 	$before = apply_filters( 'wp_multiple_authors_author_before', $before );
 	$sep = apply_filters( 'wp_multiple_authors_author_sep', $sep );
 	$after = apply_filters( 'wp_multiple_authors_author_after', $after );
	$taxonomy = apply_filters( 'wp_multiple_authors_author_after', 'guest-author' );

 	return get_the_term_list( get_the_ID(), $taxonomy, $before, $sep, $after );
}
