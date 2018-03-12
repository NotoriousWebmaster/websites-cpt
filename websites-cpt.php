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

class CDL_websites_cpt {
	public function __construct() {

		add_action('init', [$this, 'init']);	

		add_action('do_meta_boxes', [$this, 'kill_metaboxes']);	

		add_filter('post_row_actions', [$this, 'filterRowActions'], 10, 2);

		add_shortcode('websites_form', [$this, 'renderForm']);

		// handle AJAX calls for both logged in and guest users
		add_action('wp_ajax_websites_cpt_create_post', [$this, 'create_post']);
		add_action('wp_ajax_nopriv_websites_cpt_create_post', [$this, 'create_post']);

	}

	public function init() {

		// echo('In init ------------------------\n');
		// echo "_POST: " . var_export($_POST, true);

		// register WEBSITES CPT
		register_post_type('WEBSITES', [
			'menu_icon' 						=> 'dashicons-media-spreadsheet',
			'menu_position' 				=> 5, 	// below Posts
			'exclude_from_search' 	=> true,
			'publicly_queryable' 		=> false,
			'show_in_nav_menus' 		=> true,
			'show_in_admin_bar' 		=> false,
			'hierarchical' 					=> false,
			'show_ui' 							=> true,
			'show_in_menu' 					=> true,
			'labels'								=> [
				'name' 									=> 'Websites',
				'singular_name' 				=> 'Website',
				'add_new_item' 					=> 'Add New Website',
				'edit_item'							=> 'View Website Source',
				'view_item' 						=> 'View Website Source',
				'view_items'						=> 'View Website Sources',
				'all_items' 						=> 'All Websites',
				'search_items'					=> 'Search Websites',
			],
			'capabilities'					=> [
				'create_posts'					=> 'do_not_allow',
			],
			'map_meta_cap'					=> true,
			'register_meta_box_cb'	=> [$this, 'showMetaBox'],
		]);

		remove_post_type_support('websites', 'title');
		remove_post_type_support('websites', 'editor');

	}

	public function kill_metaboxes() {
		remove_meta_box( 'slugdiv', 'WEBSITES', 'normal' );
		remove_meta_box( 'submitdiv', 'WEBSITES', 'side' );
	}

	public function showMetaBox() {
		
		add_meta_box('websites-cpt-source', 'Website Source', [$this, 'sourceMetabox']);
	}

	public function sourceMetabox( $post ) {

		echo "<div class='website-source-name'>Name: <span>{$post->post_title}</span></div>";
		echo "<div class='website-source-url'>URL: <span>{$post->post_excerpt}</span></div>";

		// fetch the body of the URL
		$theBody = wp_remote_retrieve_body( wp_remote_get($post->post_excerpt) );
		if (is_wp_error($theBody) || empty($theBody)) {
			$theBody = 'ERROR: Could not retrieve source';
		} else {
			// escape < and >
			$theBody = str_replace(['<', '>'], ['&lt;', '&gt;'], $theBody);
		}

		echo "<div class='website-source-code'><pre><code>{$theBody}</code></pre></div>";
	}

	public function filterRowActions($actions, $post) {

		// $actions['view'] = "<a href=\"http://localhost/wp-admin/post.php?post={$post->ID}&amp;action=edit\" aria-label=\"Edit \“{$post->post_title}\”\">View</a>";
		unset($actions['inline hide-if-no-js']);
		return $actions;
	}

	public function renderForm() {

		wp_register_script('websites-cpt-process-form', plugins_url('js/process-form.js', __FILE__), null, null, true );

		// vars being sent to JS
		$php_vars = [
			// passing the url to call for passing the form data back to WP
			'ajaxurl' => admin_url( 'admin-ajax.php', isset( $_SERVER["HTTPS"] ) ? 'https://' : 'http://' )
		];
		wp_localize_script( 'websites-cpt-process-form', 'phpvars', $php_vars );

		wp_enqueue_script( 'websites-cpt-process-form' );

		// render form
		include_once( plugin_dir_path( __FILE__ ) . "partials/websites-form.php" );
	}

	public function create_post() {

		$res = [];
		$error = [];

		// sanitize data
		$name = sanitize_text_field($_POST['name']);
		$url = esc_url($_POST['url'], ['http', 'https']);

		if (empty($name)) {
			$error[] = 'Invalid Name field.';
		}
		if (empty($url)) {
			$error[] = 'Invalid URL field.';
		}

		// verify nonce

		// create post with name in title
		if (count($error) === 0) {
			$post_data = [
				'post_type' => 'WEBSITES',
				'post_title' => $name,
				'post_excerpt' => $url,
				'post_status' => 'publish',
			];
			$new_id = wp_insert_post($post_data);
			if ($new_id === 0) {
				$error[] = 'Problem writing post to database.';
			}
		} else {
			$new_id = 0;
		}

		$res['status'] = $new_id !== 0 && count($error) === 0 ? 'success' : 'error';
		$res['err'] = $error;

		// echo response
		echo json_encode($res);

		// die
		die();
	}

}

new CDL_websites_cpt();

