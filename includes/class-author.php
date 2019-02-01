<?php
/**
 * WP Multiple Authors Author.
 *
 * @since   0.1.0
 * @package WP_Multiple_Authors
 */



/**
 * WP Multiple Authors Author.
 *
 * @since 0.1.0
 *
 * @see   https://github.com/WebDevStudios/Taxonomy_Core
 */
class WPMA_Author extends Taxonomy_Core {
	/**
	 * Parent plugin class.
	 *
	 * @var    WP_Multiple_Authors
	 * @since  0.1.0
	 */
	protected $plugin = null;

	protected $taxonomy;

	protected $post_types;

	protected $capabilities;

	/**
	 * Constructor.
	 *
	 * Register Taxonomy.
	 *
	 * See documentation in Taxonomy_Core, and in wp-includes/taxonomy.php.
	 *
	 * @since  0.1.0
	 *
	 * @param  WP_Multiple_Authors $plugin Main plugin object.
	 */
	public function __construct( $plugin, $taxonomy = 'guest-author' ) {
		$this->plugin = $plugin;
		$this->taxonomy = $taxonomy;
		$this->post_types = apply_filters( 'wp_multiple_authors_post_types', get_post_types( array( 'public'   => true ), 'names' ) );
		$this->capabilities = apply_filters( 'wp_multiple_authors_capabilities', array(
			'manage_terms'               => 'edit_users',
			'edit_terms'                 => 'edit_users',
			'delete_terms'               => 'delete_users',
			'assign_terms'               => 'edit_posts',
		) );

		$this->hooks();

		$rewrite = array(
			'slug'                       => 'authors',
			// 'with_front'                 => true,
			// 'hierarchical'               => false,
		);

		parent::__construct(
			array(
				__( 'Author', 'wp-multiple-authors' ),
				__( 'Authors', 'wp-multiple-authors' ),
				$this->taxonomy,
			),

			array(
				'hierarchical' 							=> false,
				'show_in_menu'  						=> false,
				'capabilities'							=> $this->capabilities,
				'show_in_rest'              => true,
				'rest_base'                 => 'authors',
				'rewrite'                    => $rewrite,
				// 'rest_controller_class'     => 'WPMA_Authors_Controller',
			),

			$this->post_types
		);
	}

	/**
   * Hook in and add a metabox to add fields to taxonomy terms
   */
	function register_taxonomy_meta() {
		$prefix = 'author_';

		$cmb_term = new_cmb2_box( array(
			'id'               => $prefix . 'edit',
			'title'            => esc_html__( 'Authors', 'wp-multiple-authors' ),
			'object_types'     => array( 'term' ),
			'taxonomies'       => array( $this->taxonomy ),
			'new_term_section' => true,
		) );

		$cmb_term->add_field( array(
			'name' => esc_html__( 'Nickname', 'wp-multiple-authors' ),
			'desc' => esc_html__( '', 'wp-multiple-authors' ),
			'id'   => 'author_nickname',
			'type' => 'text',
			'attributes'        => array(
				'placeholder' 		=> __( 'Nickame', 'wp-multiple-authors' ),
			),
		) );

		$cmb_term->add_field( array(
			'name' => esc_html__( 'First Name', 'wp-multiple-authors' ),
			'desc' => esc_html__( '', 'wp-multiple-authors' ),
			'id'   => 'author_first_name',
			'type' => 'text',
			'attributes'        => array(
				'placeholder' 		=> __( 'First Name', 'wp-multiple-authors' ),
			),
		) );

		$cmb_term->add_field( array(
			'name' => esc_html__( 'Last Name', 'wp-multiple-authors' ),
			'desc' => esc_html__( '', 'wp-multiple-authors' ),
			'id'   => 'author_last_name',
			'type' => 'text',
			'attributes'        => array(
				'placeholder' 		=> __( 'Last Name', 'wp-multiple-authors' ),
			),
		) );

		$cmb_term->add_field( array(
			'name' => esc_html__( 'Email', 'wp-multiple-authors' ),
			'desc' => esc_html__( '', 'wp-multiple-authors' ),
			'id'   => 'author_email',
			'type' => 'text_email',
			'attributes'        => array(
				'placeholder' 		=> __( 'Email', 'wp-multiple-authors' ),
			),
		) );

		$cmb_term->add_field( array(
			'name' => esc_html__( 'Website', 'wp-multiple-authors' ),
			'desc' => esc_html__( '', 'wp-multiple-authors' ),
			'id'   => 'author_website',
			'type' => 'text_url',
			'attributes'        => array(
				'placeholder' 		=> __( 'Website', 'wp-multiple-authors' ),
			),
		) );

		$cmb_term->add_field( array(
			'name'  => esc_html__( 'User', 'wp-multiple-authors' ),
			'id'    => 'author',
			'desc'  => esc_html__( 'Type the name of a user to associate with this author', 'wp-multiple-authors' ),
			'type'  => 'user_select_text',
			'options' => array(),
		) );

		if( !class_exists( 'WP_Term_Images' ) ) {
			$cmb_term->add_field( array(
				'name' => esc_html__( 'Profile Picture', 'wp-multiple-authors' ),
				'desc' => esc_html__( 'Add author picture (optional)', 'wp-multiple-authors' ),
				'id'   => 'image',
				'type' => 'file',
			) );
		}

		// $cmb_term->add_field( array(
		// 	'name'     => esc_html__( 'Extra Info', 'wp-multiple-authors' ),
		// 	'desc'     => esc_html__( 'field description (optional)', 'wp-multiple-authors' ),
		// 	'id'       => $prefix . 'extra_info',
		// 	'type'     => 'title',
		// 	'on_front' => false,
		// ) );
		// $cmb_term->add_field( array(
		// 	'name' => esc_html__( 'Arbitrary Term Field', 'wp-multiple-authors' ),
		// 	'desc' => esc_html__( 'field description (optional)', 'wp-multiple-authors' ),
		// 	'id'   => $prefix . 'term_text_field',
		// 	'type' => 'text',
		// ) );
	}

	public function register_post_metabox() {
		$prefix = 'post_author_meta_';

		if( function_exists( 'acf_add_local_field_group' ) ) {

			$location = [];

			foreach( $this->post_types as $post_type ) {
				$location[] = array(
					array(
						'param' 		=> 'post_type',
						'operator' 	=> '==',
						'value' 		=> $post_type,
					)
				);
			}

			acf_add_local_field_group( array(
				'key' 			=> 'group_post_authors',
				'title' 		=> esc_html__( 'Authors', 'wp-multiple-authors' ),
				'fields' 		=> array(
					array(
						'key' 					=> 'field_authors',
						'label' 				=> esc_html__( 'Assign Author(s)', 'wp-multiple-authors' ),
						'name' 					=> 'authors',
						'type' 					=> 'taxonomy',
						'instructions' 	=> '',
						'required' 			=> 1,
						'conditional_logic' => 0,
						'wrapper' => array(
							'width' => '',
							'class' => '',
							'id' => '',
						),
						'taxonomy' 			=> $this->taxonomy,
						'field_type' 		=> 'multi_select',
						'allow_null' 		=> 0,
						'add_term' 			=> 1,
						'save_terms' 		=> 1,
						'load_terms' 		=> 0,
						'return_format' => 'id',
						'multiple' 			=> 1,
					),
				),
				'location'					=> $location,
				'menu_order' 				=> 0,
				'position' 					=> 'side',
				'style' 						=> 'default',
				'label_placement' 	=> 'top',
				'instruction_placement' => 'label',
				'hide_on_screen' 		=> '',
				'active' 						=> 1,
				'description' 			=> '',
			) );

		}

		// $cmb = new_cmb2_box( array(
		// 	'id'              => 'test_edit_document',
		// 	'title'           => esc_html__( 'Authors', 'wp-multiple-authors' ),
		// 	'object_types'    => $this->post_types,
		// 	'show_names'      => false,
		// 	'remove_box_wrap' => true,
		// 	'context'         => 'side',
		// 	// 'show_in_rest' 		=> WP_REST_Server::READABLE,
		// ) );
		//
		// $cmb->add_field( array(
		// 	'name'    					=> esc_html__( 'Author', 'wp-multiple-authors' ),
		// 	'id'      					=> $prefix . 'tags',
		// 	'type'    					=> 'taxonomy_select',
		// 	'taxonomy'					=> $this->taxonomy,
		// 	'remove_default'    => false,
		// 	// 'options'						=> $this->get_author_terms( $this->taxonomy ),
		// 	'attributes'        => array(
		// 		'multiple'    		=> true,
		// 		'placeholder' 		=> __( 'Select an Author', 'wp-multiple-authors' ),
		// 		'data-validation' => 'required',
		// 	),
		// ) );

	}

	/**
	 * Add Admin Menu
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function add_admin_menu() {
		add_menu_page(
				__( 'Authors', 'wp-multiple-authors' ),
				__( 'Authors', 'wp-multiple-authors' ),
				apply_filters( 'wp_multiple_authors_menu_capabilities', 'list_users' ),
				"edit-tags.php?taxonomy={$this->taxonomy}",
				'',
				'dashicons-id',
				apply_filters( 'wp_multiple_authors_menu_position', 65 )
		);
	}

	/**
	 * Highlight the Authors Admin Menu
	 *
	 * @since 1.0.0
	 *
	 * @param  string $parent_file
	 * @return string $parent_file
	 */
	public function highlight_menu( $parent_file ) {
		if ( get_current_screen()->taxonomy == $this->taxonomy ) {
			$parent_file = "edit-tags.php?taxonomy={$this->taxonomy}";
		}
		return $parent_file;
	}

	/**
	 * Set up Custom Fields
	 *
	 * @since 0.1.0
	 *
	 * @return array
	 */
	public function set_fields() {

		$fields = array(
			array(
				'key'     		=> 'first_name',
				'label'   		=> __( 'First Name', 'wp-multiple-authors' ),
				'type'    		=> 'text',
			),
			array(
				'key'     		=> 'last_name',
				'label'   		=> esc_html__( 'Last Name', 'wp-multiple-authors' ),
				'type'    		=> 'text',
			),
			array(
				'key'     		=> 'nickname',
				'label'   		=> esc_html__( 'Nickname', 'wp-multiple-authors' ),
				'type'    		=> 'text',
			),
			array(
				'key'     		=> 'display_name',
				'label'   		=> esc_html__( 'Display Name', 'wp-multiple-authors' ),
				'type'    		=> 'text',
			),
			array(
				'key'     		=> 'email',
				'label'   		=> esc_html__( 'Email', 'wp-multiple-authors' ),
				'type'    		=> 'text_email',
			),
			array(
				'key'     		=> 'website',
				'label'   		=> esc_html__( 'Website', 'wp-multiple-authors' ),
				'type'    		=> 'text_url',
			),
			array(
				'key'     		=> 'image',
				'label'   		=> esc_html__( 'Profile Picture', 'wp-multiple-authors' ),
				'description' => esc_html__( 'Add author picture (optional)', 'wp-multiple-authors' ),
				'type'    		=> 'file',
			),
		);
		return apply_filters( 'wp_multiple_authors_set_meta_fields', $fields );
	}

	/**
	 * Add Filter to Replace Label Text
	 *
	 * @since 0.1.0
	 *
	 * @return void
	 */
	function modify_term_labels() {
		add_filter( 'gettext', array( $this, 'term_label_gettext' ), 10, 2 );
	}
	/**
	 * Replace Label Text
	 *
	 * @since 0.1.0
	 *
	 * @param  string $translation
	 * @param  string $original
	 * @return string
	 */
	function term_label_gettext( $translation, $original ) {
		global $current_screen;

		if( "edit-{$this->taxonomy}" === $current_screen->id ) {
			if ( 'Name' == $original ) {
				return __( 'Display Name', 'wp-multiple-authors' );
			}
			if ( 'Description' == $original ) {
				return __( 'Biographical Info', 'wp-multiple-authors' );
			}
			if ( 'Image' == $original ) {
				return __( 'Profile Picture', 'wp-multiple-authors' );
			}
		}

		return $translation;
	}

	/**
	 * Filters the author's name. Return a string containing the names of all authors
	 * associated with the current post, or empty if there are none.
	 *
	 * @since 0.1.0
	 *
	 * @uses wpma_post_authors()
	 *
	 * @param string $author The original name.
	 * @return string the URL to the authors term(s) page.
	 */
	static function the_author( $author ) : string {
		$authors = wpma_post_authors( get_the_ID() );

		$text = '';
		foreach ( $authors as $author ) {

			if ( '' !== $text ) {
				$text .= ', ';
			}

			$text .= $author->name;
		}

		return $text;
	}

	/**
	 * Filter `the_author`
	 *  Display the `guest-author` taxonomy terms for the post rather than WP user
	 *
	 * @since 0.1.0
	 *
	 * @uses wpma_the_author()
	 *
	 * @return string
	 */
	public function filter_the_author() {
		return wpma_the_author();
	}

	/**
	 * Filters the link to the author posts page, change it from the link to the user
	 * which published the post to the link to the relevant `guest-author` term page.
	 *
	 * @since 0.1.0
	 *
	 * @uses wpma_post_authors()
	 *
	 * @param string $link HTML link.
	 * @return string $html HTML linking to the post author's term page.
	 */
	public function filter_the_author_posts_link( $link ) : string {

		$authors = wpma_post_authors( get_the_ID() );

		$html = '';
		foreach ( $authors as $author ) {

			if ( '' !== $html ) {
				$html .= ', ';
			}

			$html .= sprintf( '<a href="%1$s" title="%2$s" rel="author">%3$s</a>',
				esc_url( get_term_link( $author, $this->taxonomy ) ),
				/* translators: %s: author's name */
				esc_attr( sprintf( __( 'Content by %s', 'wp-multiple-authors' ), $author->name ) ),
				$author->name
			);
		}

		return $html;
	}

	/**
 * Get a list of terms
 *
 * Generic function to return an array of taxonomy terms formatted for CMB2.
 * Simply pass in your get_terms arguments and get back a beautifully formatted
 * CMB2 options array.
 *
 * @param string|array $taxonomies Taxonomy name or list of Taxonomy names
 * @param  array|string $query_args Optional. Array or string of arguments to get terms
 * @return array CMB2 options array
 */
	function get_author_terms( $taxonomies, $query_args = '' ) {
		$defaults = array(
			'hide_empty' => false
		);
		$args = wp_parse_args( $query_args, $defaults );
		$terms = get_terms( $taxonomies, $args );
		$terms_array = array();
		if ( ! empty( $terms ) ) {
			foreach ( $terms as $term ) {
				$terms_array[$term->term_id] = $term->name;
			}
		}
		return $terms_array;
	}

	/**
	 * Initiate our hooks.
	 *
	 * @since 0.1.0
	 */
	public function hooks() {
		add_action( 'cmb2_admin_init', array( $this, 'register_taxonomy_meta' ) );
		add_action( 'cmb2_admin_init', array( $this, 'register_post_metabox' ) );
		add_action( 'parent_file', array( $this, 'highlight_menu' ) );
		add_action( 'admin_menu', array( $this, 'add_admin_menu' ) );
		add_action( 'admin_head-edit-tags.php', array( $this, 'modify_term_labels' ) );
		add_action( 'admin_head-term.php', array( $this, 'modify_term_labels' ) );

		add_filter( 'the_author', array( $this, 'filter_the_author' ) );
		add_filter( 'the_author_posts_link', array( $this, 'filter_the_author_posts_link' ) );

		// Add support for Guest Authors in RSS feeds.
		// add_filter( 'the_author', array( $this, 'filter_the_author_rss' ), 15 );
		// add_action( 'rss2_item',  array( $this, 'action_add_rss_guest_authors' ) );

	}

}
