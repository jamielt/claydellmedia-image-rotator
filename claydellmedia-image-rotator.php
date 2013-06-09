<?php
/*
Plugin Name: Claydell Media Image Rotator
Plugin URI: http://jamiethompson.com/claydellmedia-image-rotator-wordpress-plugin/
Description: Claydell Media Image Rotator for WordPress is an image rotator.
Version: 0.1
Author: Jamie Thompson
Author URI: http://jamiethompson.com/
License: GPL v3
*/

/*  Copyright 2013 Jamie Thompson (email : jamie@jamiethompson.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 3, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

/**
 * CLAYDELL MEDIA IMAGE ROTATOR CONSTANTS
 */

// Plugin Folder Path
if ( !defined( 'CLAYDELLMEDIA_IMAGE_ROTATOR_PLUGIN_DIR' ) ) {
	define( 'CLAYDELLMEDIA_IMAGE_ROTATOR_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
}

// Plugin Folder URL
if ( !defined( 'CLAYDELLMEDIA_IMAGE_ROTATOR_PLUGIN_URL' ) ) {
	define( 'CLAYDELLMEDIA_IMAGE_ROTATOR_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
}

// Plugin Version
if ( !defined( 'CLAYDELLMEDIA_IMAGE_ROTATOR_VERSION' ) ) {
	define( 'CLAYDELLMEDIA_IMAGE_ROTATOR_VERSION', '0.1' );
}

/**
 * REGISTER & ENQUEUE CLAYDELL MEDIA IMAGE ROTATOR SCRIPTS/STYLES
 */
function claydellmedia_image_rotator_scripts() {
	wp_register_style( 'claydellmedia-image-rotator-style',  CLAYDELLMEDIA_IMAGE_ROTATOR_PLUGIN_URL . 'css/claydellmedia-image-rotator.css', array(  ), CLAYDELLMEDIA_IMAGE_ROTATOR_VERSION );
	
	wp_enqueue_script( 'jquery-ui-tabs-rotate-script', CLAYDELLMEDIA_IMAGE_ROTATOR_PLUGIN_URL .  '/js/jquery-ui-tabs-rotate.js', array( 'jquery' ), CLAYDELLMEDIA_IMAGE_ROTATOR_VERSION, false );
	
	wp_enqueue_script( 'rotator-script', CLAYDELLMEDIA_IMAGE_ROTATOR_PLUGIN_URL .  '/js/jquery-ui-tabs-rotator.js', array( 'jquery' ), CLAYDELLMEDIA_IMAGE_ROTATOR_VERSION, false );
	
	wp_enqueue_script('jquery');
	wp_enqueue_script('jquery-ui-core');
	wp_enqueue_script('jquery-ui-tabs');
		
	wp_enqueue_style( 'claydellmedia-image-rotator-style' );	
}
add_action( 'wp_enqueue_scripts', 'claydellmedia_image_rotator_scripts' );

/**
 * LOAD CLAYDELL MEDIA IMAGE ROTATOR
 */

function claydellmedia_image_rotator() {

/**
 * Register a custom taxonomy for featuring pages
 */
register_taxonomy(
	'featured',
	'page',
	array(
		'labels' => array(
			'name' => _x( 'Featured', 'claydellmedia' ),
		),
		'public' => false,
	)
);

/**
 * Set a default term for the Featured Page taxonomy
 */
function claydellmedia_featured_term() {
	wp_insert_term(
		'Featured',
		'featured'
	);
}
add_action( 'after_setup_theme', 'claydellmedia_featured_term' );

/**
 * Add a custom meta box for the Featured Page taxonomy
 */
function claydellmedia_add_meta_mox() {
	add_meta_box(
		'claydellmedia-featured',
		__( 'Featured Page', 'claydellmedia' ),
		'claydellmedia_create_meta_box',
		'page',
		'side',
		'core'
	);
}
add_action( 'add_meta_boxes', 'claydellmedia_add_meta_mox' );

/**
 * Create a custom meta box for the Featured Page taxonomy
 */
function claydellmedia_create_meta_box( $post ) {
	
	// Use nonce for verification
  	wp_nonce_field( 'claydellmedia_featured_page', 'claydellmedia_featured_page_nonce' );

	// Retrieve the metadata values if the exist
	$use_as_feature = get_post_meta( $post->ID, '_use_as_feature', true );
	
	?>
		<label for="use_as_feature">
			<input type="checkbox" name="use_as_feature" id="use_as_feature" <?php checked( 'on', $use_as_feature ); ?> />
			<?php printf( __( 'Feature on the %1$s front page', 'claydellmedia' ), '<em>' . get_bloginfo( 'title' ) . '</em>' ); ?>
		</label>
	<?php
}

/**
 * Save the Featured Page meta box data
 */
function claydellmedia_save_meta_box_data( $post_id ) {

	// verify this came from the our screen and with proper authorization,
	// because save_post can be triggered at other times
	if ( ! wp_verify_nonce( $_POST['claydellmedia_featured_page_nonce'], 'claydellmedia_featured_page' ) )
		return $post_id;

	// verify if this is an auto save routine. 
	// If it is our form has not been submitted, so we dont want to do anything
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) 
		return $post_id;
		
	// Check permissions
	if ( 'page' == $_POST['post_type'] ) {
		if ( ! current_user_can( 'edit_page', $post_id ) )
			return $post_id;
	} else {
		if ( ! current_user_can( 'edit_post', $post_id ) )
			return $post_id;
	}

	// OK, we're authenticated: we need to find and save the data

	// Update use_as_feature value, default is off
	$use_as_feature = isset( $_POST['use_as_feature'] ) ? $_POST['use_as_feature'] : 'off';
	update_post_meta( $post_id, '_use_as_feature', $use_as_feature ); // Save the data

	if ( 'on' == $use_as_feature ) {
		// Add the Featured term to this post
		wp_set_object_terms( $post_id, 'Featured', 'featured' );
	} elseif ( 'off' == $use_as_feature ) {
		// Let's not use that term then
		wp_delete_object_term_relationships( $post_id, 'featured' );
	}
		
}
add_action( 'save_post', 'claydellmedia_save_meta_box_data' );
// end claydellmedia_image_rotator()

/**
 * I18N - LOCALIZATION
 */
load_plugin_textdomain( 'claydellmedia-image-rotator', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );

?>