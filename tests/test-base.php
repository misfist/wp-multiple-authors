<?php
/**
 * WP_Multiple_Authors.
 *
 * @since   0.1.0
 * @package WP_Multiple_Authors
 */
class WP_Multiple_Authors_Test extends WP_UnitTestCase {

	/**
	 * Test if our class exists.
	 *
	 * @since  0.1.0
	 */
	function test_class_exists() {
		$this->assertTrue( class_exists( 'WP_Multiple_Authors') );
	}

	/**
	 * Test that our main helper function is an instance of our class.
	 *
	 * @since  0.1.0
	 */
	function test_get_instance() {
		$this->assertInstanceOf(  'WP_Multiple_Authors', wp_multiple_authors() );
	}

	/**
	 * Replace this with some actual testing code.
	 *
	 * @since  0.1.0
	 */
	function test_sample() {
		$this->assertTrue( true );
	}
}
