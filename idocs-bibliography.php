<?php /*
--------------------------------------------------------------------------------
Plugin Name: i-Docs Bibliography
Plugin URI: https://i-docs.org
Description: Provides a bibliography for the i-Docs website.
Author: Christian Wach
Version: 0.1
Author URI: https://haystack.co.uk
Text Domain: idocs-bibliography
Domain Path: /languages
--------------------------------------------------------------------------------
*/



// Set our version here.
define( 'IDOCS_BIBLIOGRAPHY_VERSION', '0.1' );

// Store reference to this file.
if ( ! defined( 'IDOCS_BIBLIOGRAPHY_FILE' ) ) {
	define( 'IDOCS_BIBLIOGRAPHY_FILE', __FILE__ );
}

// Store URL to this plugin's directory.
if ( ! defined( 'IDOCS_BIBLIOGRAPHY_URL' ) ) {
	define( 'IDOCS_BIBLIOGRAPHY_URL', plugin_dir_url( IDOCS_BIBLIOGRAPHY_FILE ) );
}
// Store PATH to this plugin's directory.
if ( ! defined( 'IDOCS_BIBLIOGRAPHY_PATH' ) ) {
	define( 'IDOCS_BIBLIOGRAPHY_PATH', plugin_dir_path( IDOCS_BIBLIOGRAPHY_FILE ) );
}



/**
 * i-Docs Bibliography Class.
 *
 * A class that encapsulates plugin functionality.
 *
 * @since 0.1
 */
class iDocs_Bibliography {

	/**
	 * Custom Post Type object.
	 *
	 * @since 0.1
	 * @access public
	 * @var object $cpt The Custom Post Type object.
	 */
	public $cpt;

	/**
	 * Advanced Custom Fields object.
	 *
	 * @since 0.1
	 * @access public
	 * @var object $cpt The Advanced Custom Fields object.
	 */
	public $acf;



	/**
	 * Constructor.
	 *
	 * @since 0.1
	 */
	public function __construct() {

		// Initialise on "plugins_loaded".
		add_action( 'plugins_loaded', [ $this, 'initialise' ] );

	}



	/**
	 * Do stuff on plugin init.
	 *
	 * @since 0.1
	 */
	public function initialise() {

		// Only do this once.
		static $done;
		if ( isset( $done ) AND $done === true ) {
			return;
		}

		// Load translation.
		$this->translation();

		// Include files.
		$this->include_files();

		// Set up objects and references.
		$this->setup_objects();

		/**
		 * Broadcast that this plugin is now loaded.
		 *
		 * @since 0.1
		 */
		do_action( 'idocs_bibliography_loaded' );

		// We're done.
		$done = true;

	}



	/**
	 * Enable translation.
	 *
	 * @since 0.1
	 */
	public function translation() {

		// Load translations.
		load_plugin_textdomain(
			'idocs-bibliography', // Unique name.
			false, // Deprecated argument.
			dirname( plugin_basename( IDOCS_BIBLIOGRAPHY_FILE ) ) . '/languages/' // Relative path to files.
		);

	}



	/**
	 * Include files.
	 *
	 * @since 0.1
	 */
	public function include_files() {

		// Include CPT class.
		include IDOCS_BIBLIOGRAPHY_PATH . 'includes/idocs-bibliography-cpt.php';

		// Include ACF class.
		include IDOCS_BIBLIOGRAPHY_PATH . 'includes/idocs-bibliography-acf.php';

	}



	/**
	 * Set up this plugin's objects.
	 *
	 * @since 0.1
	 */
	public function setup_objects() {

		// Init CPT object.
		$this->cpt = new iDocs_Bibliography_CPT( $this );

		// Init ACF object.
		$this->acf = new iDocs_Bibliography_ACF( $this );

	}



	/**
	 * Perform plugin activation tasks.
	 *
	 * @since 0.1
	 */
	public function activate() {

		// Maybe init.
		$this->initialise();

		// Pass through.
		$this->cpt->activate();

	}



	/**
	 * Perform plugin deactivation tasks.
	 *
	 * @since 0.1
	 */
	public function deactivate() {

		// Maybe init.
		$this->initialise();

		// Pass through.
		$this->cpt->deactivate();

	}



}



/**
 * Utility to get a reference to this plugin.
 *
 * @since 0.1
 *
 * @return iDocs_Bibliography $idocs_bibliography The plugin reference.
 */
function idocs_bibliography() {

	// Store instance in static variable.
	static $idocs_bibliography = false;

	// Maybe return instance.
	if ( false === $idocs_bibliography ) {
		$idocs_bibliography = new iDocs_Bibliography();
	}

	// --<
	return $idocs_bibliography;

}



// Initialise plugin now.
idocs_bibliography();

// Activation.
register_activation_hook( __FILE__, [ idocs_bibliography(), 'activate' ] );

// Deactivation.
register_deactivation_hook( __FILE__, [ idocs_bibliography(), 'deactivate' ] );

// Uninstall uses the 'uninstall.php' method.
// See: http://codex.wordpress.org/Function_Reference/register_uninstall_hook



