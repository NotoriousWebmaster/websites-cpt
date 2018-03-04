<?php
/*
Plugin Name: WEBSITES CPT
Plugin URI: http://notoriouswebmaster.com
Description: A skills assessment exercise: Create the WEBSITES Custom Post Type.
Version: 1.0
Author: A. Alfred Ayache
Author URI: http://notoriouswebmaster.com
License: TBD
*/

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

class CDL_websites_cpt {
	public function __construct() {

		add_action('init', [$this, 'init']);	

		add_action('do_meta_boxes', [$this, 'kill_metaboxes']);	

		add_filter('post_row_actions', [$this, 'filterRowActions'], 10, 2);
	}

	public function init() {
		register_post_type('WEBSITES', [
			'menu_icon' 						=> 'dashicons-media-spreadsheet',
			'menu_position' 				=> 5, 	// below Posts
			'exclude_from_search' 	=> true,
			'publicly_queryable' 		=> false,
			'show_in_nav_menus' 		=> true,
			'show_in_admin_bar' 		=> false,
			'hierarchical' 					=> false,
			// 'supports' 							=> [],
			'show_ui' 							=> true,
			'show_in_menu' 					=> true,
			'labels'								=> [
				'name' 									=> 'Websites',
				'singular_name' 				=> 'Website',
				'add_new_item' 					=> 'Add New Website',
				'view_item' 						=> 'View Website Source',
				'view_items'						=> 'View Website Sources',
				'all_items' 						=> 'All Websites',
				'search_items'					=> 'Search Websites',
			],
			/*
			'capabilities'					=> [ 
				'create_posts' 					=> false,
				'edit_post'							=> true,
				'read_post'							=> true,
				'delete_post'						=> true,
				'edit_posts'						=> true,
				'edit_others_posts'			=> true,
			],
			*/
			'register_meta_box_cb'	=> [$this, 'showMetaBox'],
		]);

		
	}

	public function kill_metaboxes() {
		remove_meta_box( 'slugdiv', 'WEBSITES', 'normal' );
		remove_meta_box( 'submitdiv', 'WEBSITES', 'side' );
	}

	public function showMetaBox() {
		echo 'Here I am in the showMetaBox -----------------';
	}

	public function filterRowActions($actions, $post) {
		global $oLog;

		$oLog->logrow('actions', $actions);
		$actions['view'] = "<a href=\"http://localhost/wp-admin/post.php?post={$post->ID}&amp;action=edit\" aria-label=\"Edit \“{$post->post_title}\”\">View</a>";
		$oLog->logrow('post', $post);
		return $actions;
	}

}

$websites_CPT = new CDL_websites_cpt();

