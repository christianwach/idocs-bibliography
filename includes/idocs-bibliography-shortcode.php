<?php

/**
 * iDocs Bibliography Shortcodes Class.
 *
 * A class that encapsulates the iDocs Bibliography Shortcode.
 *
 * @since 0.1
 *
 * @package iDocs_Bibliography
 */
class iDocs_Bibliography_Shortcode {

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
	public $shortcode_name = 'idocs_citation';



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
		add_action( 'enqueue_shortcode_ui', [ $this, 'shortcake_scripts' ] );

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
	 * @return str $team The HTML markup for the shortcode.
	 */
	public function shortcode_render( $attr, $content = null ) {

		// Init return.
		$content = '';

		// Init defaults.
		$defaults = array(
			'id'	=> '',
		);

		// Parse attributes.
		$shortcode_atts = shortcode_atts( $defaults, $attr, 'idocs_citation' );

		// Get the post.
		$citation_post = get_post( $id );

		// Check we got one.
		if ( is_object( $citation_post ) ) {

			// Set it up
			setup_postdata( $citation_post );

			// We need to manually apply our content filter because $post is the
			// object for the post into which the video has been embedded
			$content = apply_filters( 'the_content', get_the_content() );

			// reset just in case
			wp_reset_postdata();

		}

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
		add_filter( 'mce_css', array( $this, 'shortcake_styles' ) );

		// Register this shortcode.
		shortcode_ui_register_for_shortcode(

			// Shortcode name.
			$this->shortcode_name,

			// Settings.
			array(

				// Window title.
				'label' => __( 'Citation', 'idocs-bibliography' ),

				// Icon.
				'listItemImage' => 'dashicons-format-quote',

				// Window elements.
				'attrs' => array(

					// Citations list.
					array(
						'label' => __( 'Select Citation', 'idocs-bibliography' ),
						'attr'  => 'id',
						'type'  => 'select',
						'options' => $this->shortcake_select(),
						'description' => __( 'Please select a Citation.', 'idocs-bibliography' )
					),

				),

			)

		);

	}



	/**
	 * Get options array for the select.
	 *
	 * @since 0.1
	 *
	 * @return array $options The properly formatted array for the select.
	 */
	public function shortcake_select() {

		// init return
		$options = array(
			array( 'value' => '', 'label' => __( 'None', 'idocs-bibliography' ) ),
		);

		// init query args
		$site_args = array(
			'archived' => 0,
			'spam' => 0,
			'deleted' => 0,
			'public' => 1,
		);

		/**
		 * Apply plugin-wide $site_args filter.
		 *
		 * @since 0.1
		 *
		 * @param array $site_args The arguments used to query the sites.
		 */
		$site_args = apply_filters( 'wpncd_filter_site_args', $site_args );

		/**
		 * Allow the $site_args to be specifically filtered here.
		 *
		 * @since 0.1
		 *
		 * @param array $site_args The arguments used to query the sites.
		 */
		$site_args = apply_filters( 'wpncd_shortcake_select_sites_for_sites_args', $site_args );

		// get sites
		$sites = get_sites( $site_args );

		// add data for each site
		foreach( $sites AS $site ) {
			$options[] = array(
				'value' => $site->blog_id,
				'label' => esc_html( get_blog_details( $site->blog_id )->blogname ),
			);
		}

		// --<
		return $options;

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

		// add our styles to TinyMCE
		$mce_css .= ', ' . IDOCS_BIBLIOGRAPHY_URL . 'assets/css/idocs-bibliography.css';

		// --<
		return $mce_css;

	}



} // Class ends.



