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
	 * Category name.
	 *
	 * @since 0.1
	 * @access public
	 * @var str $taxonomy_name The name of the Custom Hierarchical Taxonomy.
	 */
	public $taxonomy_name = 'citationcat';

	/**
	 * Tag name.
	 *
	 * @since 0.1
	 * @access public
	 * @var str $taxonomy_name The name of the Custom Free Taxonomy.
	 */
	public $tag_name = 'citationtag';



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

		// Create taxonomy.
		add_action( 'init', [ $this, 'tag_create' ] );

		// Fix hierarchical taxonomy metabox display.
		add_filter( 'wp_terms_checklist_args', [ $this, 'tag_fix_metabox' ], 10, 2 );

		// Add a filter to the wp-admin listing table.
		add_action( 'restrict_manage_posts', [ $this, 'tag_filter_post_type' ] );

		// Add feature image size.
		//add_action( 'after_setup_theme', [ $this, 'feature_image_create' ] );

		// Filter the title and replace with full Citation.
		add_filter( 'the_title', [ $this, 'the_title' ], 10, 2 );

		// Filter the author and replace with Citation authors.
		add_filter( 'the_author', [ $this, 'the_author' ] );

		// Filter the author and replace with Citation authors.
		add_filter( 'get_the_date', [ $this, 'get_the_date' ], 10, 3 );

		// Alter the order that entries are listed.
		add_action( 'pre_get_posts', [ $this, 'posts_order' ], 10, 1 );

		// Add Citation Authors column to List Table.
		add_filter( 'manage_edit-' . $this->post_type_name . '_columns', [ $this, 'table_column_add' ], 10 );

		// Add Citation aAthors to custom column in List Table.
		add_action( 'manage_' . $this->post_type_name . '_posts_custom_column', [ $this, 'table_column_populate' ], 10, 2 );

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
				'author',
				'thumbnail',
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



	// #########################################################################



	/**
	 * Create our Custom Hierarchical Taxonomy.
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
	 * Fix the Custom Hierarchical Taxonomy metabox.
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
	 * Add a filter for this Custom Hierarchical Taxonomy to the Custom Post Type listing.
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



	// #########################################################################



	/**
	 * Create our Custom Free Taxonomy.
	 *
	 * @since 0.1
	 */
	public function tag_create() {

		// Only call this once.
		static $registered;

		// Bail if already done.
		if ( $registered ) return;

		// Register a taxonomy for this CPT.
		register_taxonomy(

			// Taxonomy name.
			$this->tag_name,

			// Post type.
			$this->post_type_name,

			// Arguments.
			[

				// Same as "tag".
				'hierarchical' => false,

				// Labels.
				'labels' => [
					'name'              => _x( 'Citation Tags', 'taxonomy general name', 'idocs-bibliography' ),
					'singular_name'     => _x( 'Citation Tag', 'taxonomy singular name', 'idocs-bibliography' ),
					'search_items'      => __( 'Search Citation Tags', 'idocs-bibliography' ),
					'all_items'         => __( 'All Citation Tags', 'idocs-bibliography' ),
					'parent_item'       => __( 'Parent Citation Tag', 'idocs-bibliography' ),
					'parent_item_colon' => __( 'Parent Citation Tag:', 'idocs-bibliography' ),
					'edit_item'         => __( 'Edit Citation Tag', 'idocs-bibliography' ),
					'update_item'       => __( 'Update Citation Tag', 'idocs-bibliography' ),
					'add_new_item'      => __( 'Add New Citation Tag', 'idocs-bibliography' ),
					'new_item_name'     => __( 'New Citation Tag Name', 'idocs-bibliography' ),
					'menu_name'         => __( 'Citation Tags', 'idocs-bibliography' ),
				],

				// Rewrite rules.
				'rewrite' => [
					'slug' => 'citation-tags'
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
	 * Fix the Custom Free Taxonomy metabox.
	 *
	 * @see https://core.trac.wordpress.org/ticket/10982
	 *
	 * @since 0.1
	 *
	 * @param array $args The existing arguments.
	 * @param int $post_id The WordPress post ID.
	 */
	public function tag_fix_metabox( $args, $post_id ) {

		// If rendering metabox for our taxonomy.
		if ( isset( $args['taxonomy'] ) AND $args['taxonomy'] == $this->tag_name ) {

			// Setting 'checked_ontop' to false seems to fix this.
			$args['checked_ontop'] = false;

		}

		// --<
		return $args;

	}



	/**
	 * Add a filter for this Custom Free Taxonomy to the Custom Post Type listing.
	 *
	 * @since 0.1
	 */
	public function tag_filter_post_type() {

		// Access current post type.
		global $typenow;

		// Bail if not our post type,
		if ( $typenow != $this->post_type_name ) return;

		// Get tax object.
		$taxonomy = get_taxonomy( $this->tag_name );

		// Show a dropdown.
		wp_dropdown_categories( [
			'show_option_all' => sprintf( __( 'Show All %s', 'idocs-bibliography' ), $taxonomy->label ),
			'taxonomy' => $this->tag_name,
			'name' => $this->tag_name,
			'orderby' => 'name',
			'selected' => isset( $_GET[$this->tag_name] ) ? $_GET[$this->tag_name] : '',
			'show_count' => true,
			'hide_empty' => true,
			'value_field' => 'slug',
			'hierarchical' => 1,
		] );

	}



	// #########################################################################



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



	// #########################################################################



	/**
	 * Replace the post title with the full Citation.
	 *
	 * @since 0.2
	 *
     * @param str $title The post title.
     * @param int $id The post ID.
	 * @return str $title The modified post title.
	 */
	public function the_title( $title, $id ) {

		// Bail if not our post type.
		if ( $this->post_type_name !== get_post_type() ) {
			return $title;
		}

		// Bail if not the main loop.
		if ( ! in_the_loop() ) {
			return $title;
		}

		// Bail if single.
		if ( is_single() ) {
			return $title;
		}

		// Replace with full Citation.
		remove_filter( 'the_title', [ $this, 'the_title' ], 10 );
		$title = idocs_get_the_citation();
		add_filter( 'the_title', [ $this, 'the_title' ], 10, 2 );

		// --<
		return $title;

	}



	/**
	 * Replace the post author with Citation authors.
	 *
	 * @since 0.2
	 *
	 * @param str $display_name The existing post author's display name.
	 * @return str $display_name The modified post author's display name.
	 */
	public function the_author( $display_name ) {

		// Bail if not our post type.
		if ( $this->post_type_name !== get_post_type() ) {
			return $display_name;
		}

		// Replace with citation author(s).
		$display_name = idocs_get_the_citation_author();

		// --<
		return $display_name;

	}



	/**
	 * Replace the post date with Citation date.
	 *
	 * @since 0.2
	 *
	 * @param str $the_date The existing post date.
	 * @param str $format The existing format.
	 * @param WP_Post $post The post object.
	 * @return str $the_date The modified post date.
	 */
	public function get_the_date( $the_date, $format, $post ) {

		// Bail if not our post type.
		if ( $this->post_type_name !== get_post_type() ) {
			return $the_date;
		}

		// Get the date published.
		$citation_date = get_field( 'field_idocs_bib_year' );

		// Overwrite if populated.
		if ( ! empty( $citation_date ) ) {
			$the_date = $citation_date;
		} else {

			// Truncate date to just the year of the post date.
			$the_date = date( 'Y', strtotime( $the_date ) );

		}

		// --<
		return $the_date;

	}



	/**
	 * Replace the post date with Citation date.
	 *
	 * @since 0.2
	 *
     * @param WP_Query $query The WP_Query instance (passed by reference).
	 */
	public function posts_order( $query ) {

		// Target the Citations archive.
	    if ( ! is_admin() AND $query->is_main_query() AND is_post_type_archive( $this->post_type_name ) ) {

	    	// Order by Year Published.
	    	$query->set( 'meta_key', 'year_published' );
	    	$query->set( 'orderby', 'meta_value_num' );
	    	$query->set( 'order', 'DESC' );

			/*
			// Order by first author surname.
	    	$query->set( 'meta_key', 'authors_0_author' );
	    	$query->set( 'orderby', 'meta_value' );
	    	$query->set( 'order', 'ASC' );
	    	*/

	    }

	}



	// #########################################################################



	/**
	 * Add Citation Authors column to List Table.
	 *
	 * @since 0.2
	 *
	 * @param array $columns The existing columns.
	 * @return array $columns The modified Columns.
	 */
	public function table_column_add( $columns ) {

		// Add Citation Authors.
		$columns['citation_authors'] = __( 'Citation Author(s)', 'idocs-bibliography' );

		// --<
		return $columns;

	}



	/**
	 * Add Citation Authors to custom column in List Table.
	 *
	 * @since 0.2
	 *
	 * @param str $column The column.
	 * @param int $post_id The numeric ID of the WordPress Post.
	 */
	public function table_column_populate( $column, $post_id ) {

		// Add Citation Authors.
		if ( $column == 'citation_authors' ) {

			// Get repeating authors fields.
			$authors = get_field( 'field_idocs_bib_authors', $post_id );

			// Build author markup.
			$author_markup = '';
			if ( ! empty( $authors ) ) {
				$build = [];
				foreach( $authors AS $author ) {
					$build[] = $author['author'];
				}
				$author_markup = implode( '; ', $build ) . ' ';
			}

			// Output.
			echo $author_markup;

		}

	}



} // class iDocs_Bibliography_CPT ends.



