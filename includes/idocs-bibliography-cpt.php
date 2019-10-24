<?php

/**
 * iDocs Bibliography Custom Post Type Class
 *
 * A class that encapsulates a Custom Post Type for iDocs Bibliography.
 *
 * @package iDocs_Bibliography
 */
class iDocs_Bibliography_CPT {

	/**
	 * Plugin (calling) object.
	 *
	 * @since 0.1
	 * @access public
	 * @var object $plugin The plugin object.
	 */
	public $plugin;

	/**
	 * Custom Post Type name.
	 *
	 * @since 0.1
	 * @access public
	 * @var object $cpt The name of the Custom Post Type.
	 */
	public $post_type_name = 'citation';

	/**
	 * Taxonomy name.
	 *
	 * @since 0.1
	 * @access public
	 * @var str $taxonomy_name The name of the Custom Taxonomy.
	 */
	public $taxonomy_name = 'citationcat';



	/**
	 * Constructor.
	 *
	 * @since 0.1
	 *
	 * @param object $plugin The plugin object.
	 */
	public function __construct( $plugin ) {

		// Store reference to plugin.
		$this->plugin = $plugin;

		// Init when this plugin is loaded.
		add_action( 'idocs_bibliography_loaded', [ $this, 'register_hooks' ] );

	}



	/**
	 * Register WordPress hooks.
	 *
	 * @since 0.1
	 */
	public function register_hooks() {

		// Always create post type.
		add_action( 'init', [ $this, 'post_type_create' ] );

		// Amend the UI on our post type edit screen.
		add_action( 'do_meta_boxes', [ $this, 'post_type_ui_filter' ] );

		// Make sure our feedback is appropriate.
		add_filter( 'post_updated_messages', [ $this, 'post_type_messages' ] );

		// Make sure our UI text is appropriate.
		add_filter( 'enter_title_here', [ $this, 'post_type_title' ] );

		// Create taxonomy.
		add_action( 'init', [ $this, 'taxonomy_create' ] );

		// Fix hierarchical taxonomy metabox display.
		add_filter( 'wp_terms_checklist_args', [ $this, 'taxonomy_fix_metabox' ], 10, 2 );

		// Add a filter to the wp-admin listing table.
		add_action( 'restrict_manage_posts', [ $this, 'taxonomy_filter_post_type' ] );

		// Add feature image size.
		//add_action( 'after_setup_theme', [ $this, 'feature_image_create' ] );

	}

	/**
	 * Actions to perform on plugin activation.
	 *
	 * @since 0.1
	 */
	public function post_type_ui_filter() {

		// Remove theme metaboxes
		remove_meta_box( 'uncode_gallery_div', $this->post_type_name, 'normal' );
		remove_meta_box( '_uncode_page_options', $this->post_type_name, 'normal' );

	}



	/**
	 * Actions to perform on plugin activation.
	 *
	 * @since 0.1
	 */
	public function activate() {

		// Pass through.
		$this->post_type_create();
		$this->taxonomy_create();

		// Go ahead and flush.
		flush_rewrite_rules();

	}



	/**
	 * Actions to perform on plugin deactivation (NOT deletion).
	 *
	 * @since 0.1
	 */
	public function deactivate() {

		// Flush rules to reset.
		flush_rewrite_rules();

	}



	// #########################################################################



	/**
	 * Create our Custom Post Type.
	 *
	 * @since 0.1
	 */
	public function post_type_create() {

		// Only call this once.
		static $registered;

		// Bail if already done.
		if ( $registered ) return;

		// Set up the post type called "Citation".
		register_post_type( $this->post_type_name, [

			// Labels.
			'labels' => [
				'name'               => __( 'Citations', 'idocs-bibliography' ),
				'singular_name'      => __( 'Citation', 'idocs-bibliography' ),
				'add_new'            => __( 'Add New', 'idocs-bibliography' ),
				'add_new_item'       => __( 'Add New Citation', 'idocs-bibliography' ),
				'edit_item'          => __( 'Edit Citation', 'idocs-bibliography' ),
				'new_item'           => __( 'New Citation', 'idocs-bibliography' ),
				'all_items'          => __( 'All Citations', 'idocs-bibliography' ),
				'view_item'          => __( 'View Citation', 'idocs-bibliography' ),
				'search_items'       => __( 'Search Citations', 'idocs-bibliography' ),
				'not_found'          => __( 'No matching Citation found', 'idocs-bibliography' ),
				'not_found_in_trash' => __( 'No Citations found in Trash', 'idocs-bibliography' ),
				'menu_name'          => __( 'Citations', 'idocs-bibliography' ),
			],

			// Defaults.
			'menu_icon'   => 'dashicons-format-quote',
			'description' => __( 'A citation post type', 'idocs-bibliography' ),
			'public' => true,
			'publicly_queryable' => true,
			'exclude_from_search' => false,
			'show_ui' => true,
			'show_in_nav_menus' => true,
			'show_in_menu' => true,
			'show_in_admin_bar' => true,
			'has_archive' => true,
			'query_var' => true,
			'capability_type' => 'post',
			'hierarchical' => false,
			'menu_position' => 25,
			'map_meta_cap' => true,

			// Rewrite.
			'rewrite' => [
				'slug' => 'citations',
				'with_front' => false
			],

			// Supports.
			'supports' => [
				'title',
				//'editor',
				'excerpt',
				//'thumbnail',
				//'revisions',
			],

		] );

		//flush_rewrite_rules();

		// Flag done.
		$registered = true;

	}



	/**
	 * Override messages for a custom post type.
	 *
	 * @since 0.1
	 *
	 * @param array $messages The existing messages.
	 * @return array $messages The modified messages.
	 */
	public function post_type_messages( $messages ) {

		// Access relevant globals.
		global $post, $post_ID;

		// Define custom messages for our custom post type.
		$messages[$this->post_type_name] = [

			// Unused - messages start at index 1.
			0 => '',

			// Item updated.
			1 => sprintf(
				__( 'Citation updated. <a href="%s">View Citation</a>', 'idocs-bibliography' ),
				esc_url( get_permalink( $post_ID ) )
			),

			// Custom fields.
			2 => __( 'Custom field updated.', 'idocs-bibliography' ),
			3 => __( 'Custom field deleted.', 'idocs-bibliography' ),
			4 => __( 'Citation updated.', 'idocs-bibliography' ),

			// Item restored to a revision.
			5 => isset( $_GET['revision'] ) ?

					// Revision text.
					sprintf(
						// Translators: %s: date and time of the revision.
						__( 'Citation restored to revision from %s', 'idocs-bibliography' ),
						wp_post_revision_title( (int) $_GET['revision'], false )
					) :

					// No revision.
					false,

			// Item published.
			6 => sprintf(
				__( 'Citation published. <a href="%s">View Citation</a>', 'idocs-bibliography' ),
				esc_url( get_permalink( $post_ID ) )
			),

			// Item saved.
			7 => __( 'Citation saved.', 'idocs-bibliography' ),

			// Item submitted.
			8 => sprintf(
				__( 'Citation submitted. <a target="_blank" href="%s">Preview Citation</a>', 'idocs-bibliography' ),
				esc_url( add_query_arg( 'preview', 'true', get_permalink( $post_ID ) ) )
			),

			// Item scheduled.
			9 => sprintf(
				__( 'Citation scheduled for: <strong>%1$s</strong>. <a target="_blank" href="%2$s">Preview Citation</a>', 'idocs-bibliography' ),
				// Translators: Publish box date format, see http://php.net/date
				date_i18n( __( 'M j, Y @ G:i' ),
				strtotime( $post->post_date ) ),
				esc_url( get_permalink( $post_ID ) )
			),

			// Draft updated.
			10 => sprintf(
				__( 'Citation draft updated. <a target="_blank" href="%s">Preview Citation</a>', 'idocs-bibliography' ),
				esc_url( add_query_arg( 'preview', 'true', get_permalink( $post_ID ) ) )
			)

		];

		// --<
		return $messages;

	}



	/**
	 * Override the "Add title" label.
	 *
	 * @since 0.1
	 *
	 * @param str $title The existing title - usually "Add title".
	 * @return str $title The modified title.
	 */
	public function post_type_title( $input ) {

		// Bail if not our post type.
		if ( $this->post_type_name !== get_post_type() ) {
			return $input;
		}

		// Overwrite with our string.
		$input = __( 'Add the title of the book, paper etc', 'idocs-bibliography' );

		// --<
		return $input;

	}



	/**
	 * Create our Custom Taxonomy.
	 *
	 * @since 0.1
	 */
	public function taxonomy_create() {

		// Only call this once.
		static $registered;

		// Bail if already done.
		if ( $registered ) return;

		// Register a taxonomy for this CPT.
		register_taxonomy(

			// Taxonomy name.
			$this->taxonomy_name,

			// Post type.
			$this->post_type_name,

			// Arguments.
			[

				// Same as "category".
				'hierarchical' => true,

				// Labels.
				'labels' => [
					'name'              => _x( 'Citation Types', 'taxonomy general name', 'idocs-bibliography' ),
					'singular_name'     => _x( 'Citation Type', 'taxonomy singular name', 'idocs-bibliography' ),
					'search_items'      => __( 'Search Citation Types', 'idocs-bibliography' ),
					'all_items'         => __( 'All Citation Types', 'idocs-bibliography' ),
					'parent_item'       => __( 'Parent Citation Type', 'idocs-bibliography' ),
					'parent_item_colon' => __( 'Parent Citation Type:', 'idocs-bibliography' ),
					'edit_item'         => __( 'Edit Citation Type', 'idocs-bibliography' ),
					'update_item'       => __( 'Update Citation Type', 'idocs-bibliography' ),
					'add_new_item'      => __( 'Add New Citation Type', 'idocs-bibliography' ),
					'new_item_name'     => __( 'New Citation Type Name', 'idocs-bibliography' ),
					'menu_name'         => __( 'Citation Types', 'idocs-bibliography' ),
				],

				// Rewrite rules.
				'rewrite' => [
					'slug' => 'citation-types'
				],

				// Show column in wp-admin.
				'show_admin_column' => true,
				'show_ui' => true,

			]

		);

		//flush_rewrite_rules();

		// Flag done.
		$registered = true;

	}



	/**
	 * Fix the Custom Taxonomy metabox.
	 *
	 * @see https://core.trac.wordpress.org/ticket/10982
	 *
	 * @since 0.1
	 *
	 * @param array $args The existing arguments.
	 * @param int $post_id The WordPress post ID.
	 */
	public function taxonomy_fix_metabox( $args, $post_id ) {

		// If rendering metabox for our taxonomy.
		if ( isset( $args['taxonomy'] ) AND $args['taxonomy'] == $this->taxonomy_name ) {

			// Setting 'checked_ontop' to false seems to fix this.
			$args['checked_ontop'] = false;

		}

		// --<
		return $args;

	}



	/**
	 * Create our Feature Image size.
	 *
	 * @since 0.1
	 */
	public function feature_image_create() {

		// Define a small, square custom image size, cropped to fit.
		add_image_size(
			'idocs-bibliography-citation',
			apply_filters( 'idocs_bibliography_citation_image_width', 384 ),
			apply_filters( 'idocs_bibliography_citation_image_height', 384 ),
			true // Crop.
		);

	}



	/**
	 * Add a filter for this Custom Taxonomy to the Custom Post Type listing.
	 *
	 * @since 0.1
	 */
	public function taxonomy_filter_post_type() {

		// Access current post type.
		global $typenow;

		// Bail if not our post type,
		if ( $typenow != $this->post_type_name ) return;

		// Get tax object.
		$taxonomy = get_taxonomy( $this->taxonomy_name );

		// Show a dropdown.
		wp_dropdown_categories( [
			'show_option_all' => sprintf( __( 'Show All %s', 'idocs-bibliography' ), $taxonomy->label ),
			'taxonomy' => $this->taxonomy_name,
			'name' => $this->taxonomy_name,
			'orderby' => 'name',
			'selected' => isset( $_GET[$this->taxonomy_name] ) ? $_GET[$this->taxonomy_name] : '',
			'show_count' => true,
			'hide_empty' => true,
			'value_field' => 'slug',
			'hierarchical' => 1,
		] );

	}



} // class iDocs_Bibliography_CPT ends.



