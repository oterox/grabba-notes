<?php

/*
Plugin Name: Grabba Notes
Plugin URI: http://grillcode.com
Description: GrabbaGreen Notes ACF
Version: 0.1
Author: Javier Otero
Author URI: http://grillcode.com
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
*/

// exit if accessed directly
if( ! defined( 'ABSPATH' ) ) exit;

if( !class_exists( 'acf_plugin_notes' ) ) :

class acf_plugin_notes {

	/*
	*  __construct
	*
	*  This function will setup the class functionality
	*
	*  @type	function
	*  @date	17/02/2016
	*  @since	1.0.0
	*
	*  @param	n/a
	*  @return	n/a
	*/

	function __construct() {

		//load_plugin_textdomain( 'acf-notes', false, plugin_basename( dirname( __FILE__ ) ) . '/lang' );

		// include field
		add_action( 'acf/include_field_types', 	array( $this, 'include_field_types' ) ); // v5
		add_action( 'acf/register_fields', 		array( $this, 'include_field_types' ) ); // v4

	}

	/*
	*  include_field_types
	*
	*  This function will include the field type class
	*
	*  @type	function
	*  @date	17/02/2016
	*  @since	1.0.0
	*
	*  @param	$version (int) major ACF version. Defaults to 4
	*  @return	n/a
	*/

	function include_field_types( $version = 5 ) {

		include_once( 'fields/acf-timestamp-v' . $version . '.php' );

		include_once( 'fields/acf-hidden-v' . $version . '.php' );

	}

}

// initialize
new acf_plugin_notes();

endif;

add_action( 'acf/init', 'add_group_notes' );
function add_group_notes() {

	if( function_exists( 'acf_add_local_field_group' ) ):

		$current_user = wp_get_current_user();

		acf_add_local_field_group( array (
			'key' => 'group_notes',
			'title' => 'Administrative Notes',
			'fields' => array (
				array (
					'key' => 'field_notes_rep',
					'label' => 'Notes',
					'name' => 'notes',
					'type' => 'repeater',
					'instructions' => '',
					'required' => 0,
					'conditional_logic' => 0,
					'wrapper' => array (
						'width' => '',
						'class' => '',
						'id' => '',
					),
					'collapsed' => '',
					'min' => '',
					'max' => '',
					'layout' => 'block',
					'button_label' => 'Add Note',
					'sub_fields' => array (
						array (
							'key' => 'field_note',
							'label' => 'Note',
							'name' => 'note',
							'type' => 'textarea',
							'required' => 0,
							'rows' => 5,
							'new_lines' => '',
							'readonly' => 0,
						),
						array (
							'key' => 'field_user_id',
							'label' => 'User ID',
							'name' => 'user_id',
							'type' => 'hidden',
							'required' => 0,
							'default_value' => $current_user->ID,
							'readonly' => 1,
						),
						array (
							'key' => 'field_notes_col1',
							'label' => 'col1',
							'name' => 'col1',
							'type' => 'column',
							'column-type' => '1_3',
						),
						array (
							'key' => 'field_user_name',
							'label' => 'Created By',
							'name' => 'user_name',
							'type' => 'text',
							'required' => 0,
							'default_value' => $current_user->display_name,
							'readonly' => 1,
						),
						array (
							'key' => 'field_notes_col2',
							'label' => 'col2',
							'name' => 'col2',
							'type' => 'column',
							'column-type' => '1_3',
						),
						array (
							'key' => 'field_created_at',
							'label' => 'Created at',
							'name' => 'created_at',
							'type' => 'timestamp',
							'updateable' => false,
							'required' => 0,
							'default_value' => date('m/d/Y h:i:s a', time()),
							'readonly' => 1,
						),
						array (
							'key' => 'field_notes_col3',
							'label' => 'col3',
							'name' => 'col3',
							'type' => 'column',
							'column-type' => '1_3',
						),
						array (
							'key' => 'field_modified_at',
							'label' => 'Modified at',
							'name' => 'modified',
							'type' => 'timestamp',
							'updateable' => true,
							'required' => 0,
							'default_value' => date('m/d/Y h:i:s a', time()),
							'readonly' => 1,
						),
					),
				),
			),
			'location' => array (
				array (
					array (
						'param' => 'post_type',
						'operator' => '==',
						'value' => 'grabbastore',
					),
				),
				array (
					array (
						'param' => 'post_type',
						'operator' => '==',
						'value' => 'application',
					),
				),
				array (
					array (
						'param' => 'post_type',
						'operator' => '==',
						'value' => 'grabba-area',
					),
				),
				array (
					array (
						'param' => 'user_form',
						'operator' => '==',
						'value' => 'edit',
					),
				),
			),
			'menu_order' => 99,
			'position' => 'normal',
			'style' => 'default',
			'label_placement' => 'top',
			'instruction_placement' => 'label',
			'hide_on_screen' => '',
			'active' => 1,
			'description' => '',
		));

	endif;
}

add_action( 'acf/input/admin_head', 'update_timestamp_on_change' );
function update_timestamp_on_change() {
?>
<script type="text/javascript">
	(function($) {
		$('.acf-field-note textarea').live('change', function(e){
			e.preventDefault();
			if(e.handled === true) return false;
         e.handled = true;

				var fielddata = '*' + $(this).parent().parent().parent().find('.acf-field-modified-at input').val();
      	$(this).parent().parent().parent().find('.acf-field-modified-at input').val( fielddata );

				return false;

		});

	})(jQuery);
</script>
<?php
}

//Admin notes sync
function notes_acf_save_post( $post_id ) {

  $screen = get_current_screen();
  $notes  = get_field( 'notes', $post_id );

  switch ( $screen->id ) {
    case 'user-edit':
     //we are in the user profile page. When we update the admin notes field(repeater)
     // we have to update the related application admin notes field. We copy the array 
     // with all the admin notes

      //get the related application id for this user
      $application = get_posts(
        array(
          'post_type' => 'application',
          'posts_per_page' => -1,
          'meta_query' => array(
            array(
              'key' => 'wp_user',
              'value' => '"' . str_replace("user_", "", $post_id ) . '"',
              'compare' => 'LIKE'
            )
          )
        )
      );

      $application_id = $application[0]->ID;

      remove_action( 'save_post', array( $this, 'notes_acf_save_post' ) );

      //update the admin notes field(repeater) in the related application
      $result = update_field( 'notes', $notes, $application_id );

      add_action( 'save_post', array( $this, 'notes_acf_save_post' ) );

      break;

    case 'application':
      //We are in the Application page. When we save the admin notes we have to 
      //update the admin notes for the related user (field wp_user)

      $wp_user = get_field( 'wp_user', $post_id );

      $user_id = $wp_user[0]['ID'];

      remove_action( 'save_post', array( $this, 'notes_acf_save_post' ) );

      //update the admin notes field(repater) in the user profile
      $result = update_field( 'notes', $notes, 'user_' . $user_id );

      add_action( 'save_post', array( $this, 'notes_acf_save_post' ) );

      break;

    default:
      break;
  }

}
add_action( 'acf/save_post', 'notes_acf_save_post', 90 );
// End Admin notes sync
