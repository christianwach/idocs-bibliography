<?php

/**
 * iDocs Bibliography Citations List Shortcode Class.
 *
 * A class that encapsulates the iDocs Bibliography Shortcode which renders a
 * list of Citations.
 *
 * @since 0.1
 *
 * @package iDocs_Bibliography
 */
class iDocs_Bibliography_Shortcode_List {

	/**
	 * Plugin (calling) object.
	 *
	 * @since 0.1
	 * @access public
	 * @var object $plugin The plugin object.
	 */
	public $plugin;

	/**
	 * Shortcode name.
	 *
	 * @since 0.1
	 * @access public
	 * @var str $shortcode_name The name of the shortcode tag.
	 */
	public $shortcode_name = 'idocs_citations';



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

		// register shortcode
		add_action( 'init', [ $this, 'shortcode_register' ] );

		// Shortcake compat
		add_action( 'register_shortcode_ui', [ $this, 'shortcake' ] );
		//add_action( 'enqueue_shortcode_ui', [ $this, 'shortcake_scripts' ] );

	}



	/**
	 * Register our shortcode.
	 *
	 * @since 0.1
	 */
	public function shortcode_register() {

		// Register shortcodes.
		add_shortcode( $this->shortcode_name, [ $this, 'shortcode_render' ] );

	}



	// #########################################################################



	/**
	 * Add the citation to a page via a shortcode.
	 *
	 * @since 0.1
	 *
	 * @param array $attr The saved shortcode attributes.
	 * @param str $content The enclosed content of the shortcode.
	 * @return str $citation The HTML markup for the shortcode.
	 */
	public function shortcode_render( $attr, $content = null ) {

		// Init return.
		$content = '';

		// Init defaults.
		$defaults = array(
			'category'	=> '',
			'tag'		=> '',
			'relation'	=> 'OR',
		);

		// Parse attributes.
		$shortcode_atts = shortcode_atts( $defaults, $attr, 'idocs_citation' );

		// GetCitations for the specified category.
		$args = array(
			'post_type' => $this->plugin->cpt->post_type_name,
			'post_status' => 'publish',
			'no_found_rows' => true,
			'posts_per_page' => -1,
			'orderby' => 'title',
			'order' => 'ASC',
		);

		// Add taxonomy stuff.
		$args['tax_query'] = [
			'relation' => $shortcode_atts['relation'],
		];

		// Add category.
		if ( ! empty( $shortcode_atts['category'] ) ) {
			$args['tax_query'][] = [
				'taxonomy' => $this->plugin->cpt->taxonomy_name,
				'terms' => array_map( 'intval', explode( ',', $shortcode_atts['category'] ) ),
			];
		}

		// Add tag.
		if ( ! empty( $shortcode_atts['tag'] ) ) {
			$args['tax_query'][] = [
				'taxonomy' => $this->plugin->cpt->tag_name,
				'terms' => array_map( 'intval', explode( ',', $shortcode_atts['tag'] ) ),
			];
		}

		// Do query.
		$query = new WP_Query( $args );

		// See how we do.
		$citations = [];
		if ( $query->have_posts() ) {

			// Grab rendered citations.
			while ( $query->have_posts() ) : $query->the_post();
				$citations[] = idocs_get_the_citation();
			endwhile;

			// Sort alphabetically.
			asort( $citations );

			// Build list.
			$content = '<ul><li>' . implode( '</li><li>', $citations ) . '</li></ul>';

		}

		// Prevent weirdness.
		wp_reset_postdata();

		// --<
		return $content;

	}



	/**
	 * Add compatibility with Shortcake UI.
	 *
	 * Be aware that in Shortcode UI < 0.7.2, values chosen via selects do not
	 * "stick". At present, you will need the master branch of Shortcode UI from
	 * GitHub, which has solved this problem.
	 *
	 * @see https://github.com/wp-shortcake/shortcake/issues/747
	 *
	 * Furthermore, multi-selects do not function at all.
	 *
	 * @see https://github.com/wp-shortcake/shortcake/issues/757
	 *
	 * @since 0.1
	 */
	public function shortcake() {

		// Let's be extra-safe and bail if not present.
		if ( ! function_exists( 'shortcode_ui_register_for_shortcode' ) ) return;

		// Add styles for TinyMCE editor.
		//add_filter( 'mce_css', array( $this, 'shortcake_styles' ) );

		// Register this shortcode.
		shortcode_ui_register_for_shortcode(

			// Shortcode name.
			$this->shortcode_name,

			// Settings.
			array(

				// Window title.
				'label' => __( 'Citation List', 'idocs-bibliography' ),

				// Icon.
				'listItemImage' => 'dashicons-format-quote',

				// Window elements.
				'attrs' => array(

					// Citations category.
					array(
						'label' => __( 'List Citations in a Category', 'idocs-bibliography' ),
						'attr'  => 'category',
						'type'  => 'select',
						'options' => $this->shortcake_select_category(),
						'description' => __( 'Optionally select a Category.', 'idocs-bibliography' ),
						'meta' => array(
							'multiple' => true,
						),
					),

					// Citations tag.
					array(
						'label' => __( 'List Citations with a Tag', 'idocs-bibliography' ),
						'attr'  => 'tag',
						'type'  => 'select',
						'options' => $this->shortcake_select_tag(),
						'description' => __( 'Optionally select a Tag.', 'idocs-bibliography' ),
						'meta' => array(
							'multiple' => true,
						),
					),

					// Relationship.
					array(
						'label' => __( 'And/Or', 'idocs-bibliography' ),
						'description' => __( 'If you have selected both a Category and a Tag, you will also need to choose a relation between them. So, for example, you could show "Citations that are in the Books Category AND have the Input Device Tag." or maybe you want "Citations that are in the Books Category OR have the Documentary Films Tag."', 'idocs-bibliography' ),
						'attr' => 'relation',
						'type' => 'radio',
						'options' => array(
							array( 'value' => 'AND', 'label' => __( 'And', 'idocs-bibliography' ) ),
							array( 'value' => 'OR', 'label' => __( 'Or', 'idocs-bibliography' ) ),
						),
						'value' => '',
					),

				),

			)

		);

	}



	/**
	 * Get select array for Shortcake registration.
	 *
	 * @since 0.1
	 *
	 * @return array $options The properly formatted array for the select.
	 */
	public function shortcake_select_category() {

		// Init return.
		$options = array( '' => __( 'No Category selected', 'idocs-bibliography' ) );

		// Get all terms in our custom taxonomy.
		$hierarchy = $this->shortcake_hierarchy_get( $this->plugin->cpt->taxonomy_name );

		// Bail if empty.
		if( empty( $hierarchy ) ) {
			return $options;
		}

		// Init property to hold recursively generated array.
		$this->blah = array();

		// Parse hierarchy.
		$this->shortcake_hierarchy_parse( $hierarchy );

		// Add to options.
		$options = $options + $this->blah;

		// --<
		return $options;

	}



	/**
	 * Get select array for Shortcake registration.
	 *
	 * @since 0.1
	 *
	 * @return array $options The properly formatted array for the select.
	 */
	public function shortcake_select_tag() {

		// Init return.
		$options = array( '' => __( 'No Tag selected', 'idocs-bibliography' ) );

		// Get all terms in our custom taxonomy.
		$hierarchy = $this->shortcake_hierarchy_get( $this->plugin->cpt->tag_name );

		// Bail if empty.
		if( empty( $hierarchy ) ) {
			return $options;
		}

		// Init property to hold recursively generated array.
		$this->blah = array();

		// Parse hierarchy.
		$this->shortcake_hierarchy_parse( $hierarchy );

		// Add to options.
		$options = $options + $this->blah;

		// --<
		return $options;

	}



	/**
	 * Get the taxonomy hierarchy.
	 *
	 * @since 0.1
	 *
	 * @param str $taxonomy The taxonomy name
	 * @param int $parent The parent term ID
	 * @return array $children The children for the parent term
	 */
	public function shortcake_hierarchy_get( $taxonomy, $parent = 0 ) {

		// Init return.
		$children = array();

		// Get all terms with this parent.
		$terms = get_terms( $taxonomy, array(
			'parent' => $parent,
			'hide_empty' => 0,
		) );

		// Loop through terms.
		foreach( $terms AS $term ) {

			// Recurse to find children.
			$term->children = $this->shortcake_hierarchy_get( $taxonomy, $term->term_id );

			// Add term.
			$children[ $term->term_id ] = $term;

		}

		// --<
		return $children;

	}



	/**
	 * Parse the taxonomy hierarchy.
	 *
	 * @since 0.1
	 *
	 * @param array $hierarchy The taxonomy hierarchy
	 * @return array $options The parsed options
	 */
	public function shortcake_hierarchy_parse( $hierarchy, $prefix = '' ) {

		// Construct options array/
		foreach( $hierarchy AS $term_id => $term ) {

			// Add spacer if prefix is non-empty.
			$spacer = empty( $prefix ) ? '' : ' ';

			// Add this term.
			$this->blah[$term->term_id] = $prefix . $spacer . $term->name . ' (ID: ' . $term->term_id . ')';

			// Add its children.
			if ( ! empty( $term->children ) ) {
				$this->shortcake_hierarchy_parse( $term->children, $prefix . '-' );
			}

		}

	}



	/**
	 * Enqueue Javascript for custom functionality in Shortcake.
	 *
	 * @since 0.1
	 */
	public function shortcake_scripts() {

		wp_enqueue_script(
			'wpncd-shortcode-ui',
			IDOCS_BIBLIOGRAPHY_URL . 'assets/js/idocs-bibliography.js',
			array( 'shortcode-ui' ),
			IDOCS_BIBLIOGRAPHY_VERSION
		);

	}



	/**
	 * Add stylesheet to TinyMCE when Shortcake is active.
	 *
	 * @since 0.1
	 *
	 * @param str $mce_css The existing list of stylesheets that TinyMCE will load
	 * @return str $mce_css The modified list of stylesheets that TinyMCE will load
	 */
	public function shortcake_styles( $mce_css ) {

		// Add our styles to TinyMCE.
		$mce_css .= ', ' . IDOCS_BIBLIOGRAPHY_URL . 'assets/css/idocs-bibliography.css';

		// --<
		return $mce_css;

	}



} // Class ends.



