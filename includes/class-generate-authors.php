<?php
/**
 * WP Multiple Authors Generate Authors.
 *
 * @since   0.1.0
 * @package WP_Multiple_Authors
 */

/**
 * WP Multiple Authors Generate Authors.
 *
 * @since 0.1.0
 */
class WPMA_Generate_Authors {
	/**
	 * Parent plugin class
	 *
	 * @var   WP_Multiple_Authors
	 *
	 * @since 0.1.0
	 */
	protected $plugin = null;

	/**
	 * Constructor.
	 *
	 * @since  0.1.0
	 *
	 * @param  WP_Multiple_Authors $plugin Main plugin object.
	 */
	public function __construct( $plugin ) {
		$this->plugin = $plugin;

		// If we have WP CLI, add our commands.
		if ( $this->verify_wp_cli() ) {
			$this->add_commands();
		}
	}

	/**
	 * Check for WP CLI running.
	 *
	 * @since  0.1.0
	 *
	 * @return boolean True if WP CLI currently running.
	 */
	public function verify_wp_cli() {
		return ( defined( 'WP_CLI' ) && WP_CLI );
	}

	/**
	 * Add our commands.
	 *
	 * @since  0.1.0
	 */
	public function add_commands() {
		WP_CLI::add_command( 'wp_multiple_authors_generate_authors', array( $this, 'wp_multiple_authors_generate_authors_command' ) );
	}

	/**
	 * Create a method stub for our first CLI command.
	 *
	 * @since 0.1.0
	 */
	public function wp_multiple_authors_generate_authors_command() {

	}
}
