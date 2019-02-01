<?php
/**
 * Plugin Name: WP Multiple Authors
 * Plugin URI:  https://github.com/misfist/wp-multiple-authors
 * Description: Create authors taxonomy that can be used to assign multiple authors to posts.
 * Version:     0.1.0
 * Author:      misfist
 * Author URI:  https://patizialutz.tech
 * Donate link: https://github.com/misfist/wp-multiple-authors
 * License:     GPLv3
 * Text Domain: wp-multiple-authors
 * Domain Path: /languages
 *
 * @link    https://github.com/misfist/wp-multiple-authors
 *
 * @package WP_Multiple_Authors
 * @version 0.1.0
 *
 */

/**
 * Copyright (c) 2019 misfist (email : pea@misfist.com)
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License, version 2 or, at
 * your discretion, any later version, as published by the Free
 * Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */


// Use composer autoload.
require 'vendor/autoload.php';

/**
 * Main initiation class.
 *
 * @since  0.1.0
 */
final class WP_Multiple_Authors {

	/**
	 * Current version.
	 *
	 * @var    string
	 * @since  0.1.0
	 */
	const VERSION = '0.1.0';

	/**
	 * URL of plugin directory.
	 *
	 * @var    string
	 * @since  0.1.0
	 */
	protected $url = '';

	/**
	 * Path of plugin directory.
	 *
	 * @var    string
	 * @since  0.1.0
	 */
	protected $path = '';

	/**
	 * Plugin basename.
	 *
	 * @var    string
	 * @since  0.1.0
	 */
	protected $basename = '';

	/**
	 * Detailed activation error messages.
	 *
	 * @var    array
	 * @since  0.1.0
	 */
	protected $activation_errors = array();

	/**
	 * Singleton instance of plugin.
	 *
	 * @var    WP_Multiple_Authors
	 * @since  0.1.0
	 */
	protected static $single_instance = null;

	/**
	 * Instance of WPMA_Author
	 *
	 * @since 0.1.0
	 * @var WPMA_Author
	 */
	protected $author;

	/**
	 * Instance of WPMA_Authors_Controller
	 *
	 * @since 0.1.0
	 * @var WPMA_Authors
	 */
	protected $authors_controller;

	/**
	 * Instance of WPMA_Generate_Authors
	 *
	 * @sinceundefined
	 * @var WPMA_Generate_Authors
	 */
	protected $generate_authors;

	/**
	 * Instance of WPMA_Options
	 *
	 * @since0.1.0
	 * @var WPMA_Options
	 */
	protected $options;

	/**
	 * Creates or returns an instance of this class.
	 *
	 * @since   0.1.0
	 * @return  WP_Multiple_Authors A single instance of this class.
	 */
	public static function get_instance() {
		if ( null === self::$single_instance ) {
			self::$single_instance = new self();
		}

		return self::$single_instance;
	}

	/**
	 * Sets up our plugin.
	 *
	 * @since  0.1.0
	 */
	protected function __construct() {
		$this->basename = plugin_basename( __FILE__ );
		$this->url      = plugin_dir_url( __FILE__ );
		$this->path     = plugin_dir_path( __FILE__ );
	}

	/**
	 * Attach other plugin classes to the base plugin class.
	 *
	 * @since  0.1.0
	 */
	public function plugin_classes() {

		$this->author = new WPMA_Author( $this );
		$this->authors_controller = new WPMA_Authors_Controller( $this );
		$this->generate_authors = new WPMA_Generate_Authors( $this );
		$this->options = new WPMA_Options( $this );

		include( $this->path . 'includes/helpers.php' );
		include( $this->path . 'includes/template-tags.php' );
	} // END OF PLUGIN CLASSES FUNCTION

	/**
	 * Add hooks and filters.
	 * Priority needs to be
	 * < 10 for CPT_Core,
	 * < 5 for Taxonomy_Core,
	 * and 0 for Widgets because widgets_init runs at init priority 1.
	 *
	 * @since  0.1.0
	 */
	public function hooks() {
		add_action( 'init', array( $this, 'init' ), 0 );
	}

	/**
	 * Activate the plugin.
	 *
	 * @since  0.1.0
	 */
	public function _activate() {
		// Bail early if requirements aren't met.
		if ( ! $this->check_requirements() ) {
			return;
		}

		// Make sure any rewrite functionality has been loaded.
		flush_rewrite_rules();
	}

	/**
	 * Deactivate the plugin.
	 * Uninstall routines should be in uninstall.php.
	 *
	 * @since  0.1.0
	 */
	public function _deactivate() {
		// Add deactivation cleanup functionality here.
	}

	/**
	 * Init hooks
	 *
	 * @since  0.1.0
	 */
	public function init() {

		// Bail early if requirements aren't met.
		if ( ! $this->check_requirements() ) {
			return;
		}

		// Load translated strings for plugin.
		load_plugin_textdomain( 'wp-multiple-authors', false, dirname( $this->basename ) . '/languages/' );

		// Initialize plugin classes.
		$this->plugin_classes();
	}

	/**
	 * Check if the plugin meets requirements and
	 * disable it if they are not present.
	 *
	 * @since  0.1.0
	 *
	 * @return boolean True if requirements met, false if not.
	 */
	public function check_requirements() {

		// Bail early if plugin meets requirements.
		if ( $this->meets_requirements() ) {
			return true;
		}

		// Add a dashboard notice.
		add_action( 'all_admin_notices', array( $this, 'requirements_not_met_notice' ) );

		// Deactivate our plugin.
		add_action( 'admin_init', array( $this, 'deactivate_me' ) );

		// Didn't meet the requirements.
		return false;
	}

	/**
	 * Deactivates this plugin, hook this function on admin_init.
	 *
	 * @since  0.1.0
	 */
	public function deactivate_me() {

		// We do a check for deactivate_plugins before calling it, to protect
		// any developers from accidentally calling it too early and breaking things.
		if ( function_exists( 'deactivate_plugins' ) ) {
			deactivate_plugins( $this->basename );
		}
	}

	/**
	 * Check that all plugin requirements are met.
	 *
	 * @since  0.1.0
	 *
	 * @return boolean True if requirements are met.
	 */
	public function meets_requirements() {

		// Do checks for required classes / functions or similar.
		// Add detailed messages to $this->activation_errors array.
		return true;
	}

	/**
	 * Adds a notice to the dashboard if the plugin requirements are not met.
	 *
	 * @since  0.1.0
	 */
	public function requirements_not_met_notice() {

		// Compile default message.
		$default_message = sprintf( __( 'WP Multiple Authors is missing requirements and has been <a href="%s">deactivated</a>. Please make sure all requirements are available.', 'wp-multiple-authors' ), admin_url( 'plugins.php' ) );

		// Default details to null.
		$details = null;

		// Add details if any exist.
		if ( $this->activation_errors && is_array( $this->activation_errors ) ) {
			$details = '<small>' . implode( '</small><br /><small>', $this->activation_errors ) . '</small>';
		}

		// Output errors.
		?>
		<div id="message" class="error">
			<p><?php echo wp_kses_post( $default_message ); ?></p>
			<?php echo wp_kses_post( $details ); ?>
		</div>
		<?php
	}

	/**
	 * Magic getter for our object.
	 *
	 * @since  0.1.0
	 *
	 * @param  string $field Field to get.
	 * @throws Exception     Throws an exception if the field is invalid.
	 * @return mixed         Value of the field.
	 */
	public function __get( $field ) {
		switch ( $field ) {
			case 'version':
				return self::VERSION;
			case 'basename':
			case 'url':
			case 'path':
			case 'author':
			case 'authors_controller':
			case 'generate_authors':
			case 'helpers':
			case 'template_tags':
			case 'options':
				return $this->$field;
			default:
				throw new Exception( 'Invalid ' . __CLASS__ . ' property: ' . $field );
		}
	}
}

/**
 * Grab the WP_Multiple_Authors object and return it.
 * Wrapper for WP_Multiple_Authors::get_instance().
 *
 * @since  0.1.0
 * @return WP_Multiple_Authors  Singleton instance of plugin class.
 */
function wp_multiple_authors() {
	return WP_Multiple_Authors::get_instance();
}

// Kick it off.
add_action( 'plugins_loaded', array( wp_multiple_authors(), 'hooks' ) );

// Activation and deactivation.
register_activation_hook( __FILE__, array( wp_multiple_authors(), '_activate' ) );
register_deactivation_hook( __FILE__, array( wp_multiple_authors(), '_deactivate' ) );
