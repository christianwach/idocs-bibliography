<?php

/**
 * iDocs Bibliography ACF Class.
 *
 * A class that encapsulates all ACF functionality for iDocs Bibliography.
 *
 * @package iDocs_Bibliography
 */
class iDocs_Bibliography_ACF {

	/**
	 * Plugin (calling) object.
	 *
	 * @since 0.1
	 * @access public
	 * @var object $plugin The plugin object.
	 */
	public $plugin;



	/**
	 * Constructor.
	 *
	 * @since 0.1
	 *
	 * @param object $plugin The plugin object.
	 */
	public function __construct( $plugin ) {

		// Bail if ACF isn't found.
		if ( ! function_exists( 'acf' ) ) {
			return;
		}

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

		// Add field groups.
		add_action( 'acf/init', [ $this, 'field_groups_add' ] );

		// Add fields.
		add_action( 'acf/init', [ $this, 'fields_add' ] );

	}



	/**
	 * Add ACF Field Groups.
	 *
	 * @since 0.1
	 */
	public function field_groups_add() {

		// Attach the field group to our CPT.
		$field_group_location = [[[
			'param' => 'post_type',
			'operator' => '==',
			'value' => $this->plugin->cpt->post_type_name,
		]]];

		// Hide UI elements on our CPT edit page.
		$field_group_hide_elements = [
			'the_content',
			'excerpt',
			'discussion',
			'comments',
			'revisions',
			//'author',
			'format',
			'page_attributes',
			//'featured_image',
			'tags',
			'send-trackbacks',
		];

		// Define field group.
		$field_group = [
			'key' => 'group_idocs_bib_data',
			'title' => __( 'Bibliographic Data', 'idocs-bibliography' ),
			'fields' => [],
			//'collapsed' => 'field_idocs_bib_authors',
			'location' => $field_group_location,
			'hide_on_screen' => $field_group_hide_elements,
		];

		// Now add the group.
		acf_add_local_field_group( $field_group );

	}



	/**
	 * Add ACF Fields.
	 *
	 * @since 0.1
	 */
	public function fields_add() {

		// Define an "Author" field.
		$this->field_author_add();

		// Define an "Abstract" field.
		$this->field_abstract_add();

		// Define a "Year of Publication" field.
		$this->field_year_add();

		// Define a "Place of Publication" field.
		$this->field_place_add();

		// Define a "Publisher" field.
		$this->field_publisher_add();

		// Define a "Publication" field.
		$this->field_publication_add();

		// Define a "Volume" field.
		$this->field_volume_add();

		// Define a "Issue" field.
		$this->field_issue_add();

		// Define a "From Page" field.
		$this->field_page_from_add();

		// Define a "To Page" field.
		$this->field_page_to_add();

		// Define a "ISBN" field.
		$this->field_isbn_add();

		// Define a "Link" field.
		$this->field_link_add();

	}



	/**
	 * Add "Authors" Repeater Field.
	 *
	 * This is composed of a "repeater" field with sub-fields.
	 *
	 * @since 0.1
	 */
	public function field_author_add() {

		// Define sub field.
		$sub_field = [
			'key' => 'field_idocs_bib_author',
			'label' => __( 'Author', 'idocs-bibliography' ),
			'name' => 'author',
			'type' => 'text',
			'instructions' => '',
		];

		// Define a repeater field.
		$field = [
			'key' => 'field_idocs_bib_authors',
			'label' => __( 'Authors', 'idocs-bibliography' ),
			'name' => 'authors',
			'type' => 'repeater',
			'parent' => 'group_idocs_bib_data',
			'sub_fields' => [ $sub_field ],
		];

		// Now add field.
		acf_add_local_field( $field );

	}



	/**
	 * Add "Abstract" Field.
	 *
	 * @since 0.1
	 */
	public function field_abstract_add() {

		// Define field.
		$field = [
			'key' => 'field_idocs_bib_abstract',
			'label' => __( 'Abstract', 'idocs-bibliography' ),
			'name' => 'abstract',
			'type' => 'textarea',
			'instructions' => '',
			'parent' => 'group_idocs_bib_data',
		];

		// Now add field.
		acf_add_local_field( $field );

	}



	/**
	 * Add "Year of Publication" Field.
	 *
	 * @since 0.1
	 */
	public function field_year_add() {

		// Define field.
		$field = [
			'key' => 'field_idocs_bib_year',
			'label' => __( 'Year Published', 'idocs-bibliography' ),
			'name' => 'year_published',
			'type' => 'date_picker',
			'instructions' => '',
			'display_format' => 'Y',
			'return_format' => 'Y',
			'first_day' => 1,
			'parent' => 'group_idocs_bib_data',
		];

		// Now add field.
		acf_add_local_field( $field );

	}



	/**
	 * Add "Place of Publication" Field.
	 *
	 * @since 0.1
	 */
	public function field_place_add() {

		// Define field.
		$field = [
			'key' => 'field_idocs_bib_place',
			'label' => __( 'Place of Publication', 'idocs-bibliography' ),
			'name' => 'place_of_publication',
			'type' => 'text',
			'instructions' => '',
			'parent' => 'group_idocs_bib_data',
		];

		// Now add field.
		acf_add_local_field( $field );

	}



	/**
	 * Add "Publisher" Field.
	 *
	 * @since 0.1
	 */
	public function field_publisher_add() {

		// Define field.
		$field = [
			'key' => 'field_idocs_bib_publisher',
			'label' => __( 'Publisher', 'idocs-bibliography' ),
			'name' => 'publisher',
			'type' => 'text',
			'instructions' => '',
			'parent' => 'group_idocs_bib_data',
		];

		// Now add field.
		acf_add_local_field( $field );

	}



	/**
	 * Add "Publication" Field.
	 *
	 * @since 0.1
	 */
	public function field_publication_add() {

		// Define field.
		$field = [
			'key' => 'field_idocs_bib_publication',
			'label' => __( 'Publication', 'idocs-bibliography' ),
			'name' => 'publication',
			'type' => 'text',
			'instructions' => __( 'For example the name of the Journal. If this is a book, leave blank.', 'idocs-bibliography' ),
			'parent' => 'group_idocs_bib_data',
		];

		// Now add field.
		acf_add_local_field( $field );

	}



	/**
	 * Add "Volume" Field.
	 *
	 * @since 0.1
	 */
	public function field_volume_add() {

		// Define field.
		$field = [
			'key' => 'field_idocs_bib_volume',
			'label' => __( 'Volume', 'idocs-bibliography' ),
			'name' => 'volume',
			'type' => 'number',
			'instructions' => '',
			'parent' => 'group_idocs_bib_data',
		];

		// Now add field.
		acf_add_local_field( $field );

	}



	/**
	 * Add "Issue" Field.
	 *
	 * @since 0.1
	 */
	public function field_issue_add() {

		// Define field.
		$field = [
			'key' => 'field_idocs_bib_issue',
			'label' => __( 'Issue', 'idocs-bibliography' ),
			'name' => 'issue',
			'type' => 'number',
			'instructions' => '',
			'parent' => 'group_idocs_bib_data',
		];

		// Now add field.
		acf_add_local_field( $field );

	}



	/**
	 * Add "Page (From)" Field.
	 *
	 * @since 0.1
	 */
	public function field_page_from_add() {

		// Define field.
		$field = [
			'key' => 'field_idocs_bib_page_from',
			'label' => __( 'Starting Page Reference', 'idocs-bibliography' ),
			'name' => 'page_from',
			'type' => 'number',
			'instructions' => '',
			'parent' => 'group_idocs_bib_data',
		];

		// Now add field.
		acf_add_local_field( $field );

	}



	/**
	 * Add "Page (To)" Field.
	 *
	 * @since 0.1
	 */
	public function field_page_to_add() {

		// Define field.
		$field = [
			'key' => 'field_idocs_bib_page_to',
			'label' => __( 'Ending Page Reference', 'idocs-bibliography' ),
			'name' => 'page_to',
			'type' => 'number',
			'instructions' => __( 'Leave blank if this citation only references a single page.', 'idocs-bibliography' ),
			'parent' => 'group_idocs_bib_data',
		];

		// Now add field.
		acf_add_local_field( $field );

	}



	/**
	 * Add "ISBN" Field.
	 *
	 * @since 0.1
	 */
	public function field_isbn_add() {

		// Define field.
		$field = [
			'key' => 'field_idocs_bib_isbn',
			'label' => __( 'ISBN', 'idocs-bibliography' ),
			'name' => 'isbn',
			'type' => 'text',
			'instructions' => '',
			'parent' => 'group_idocs_bib_data',
		];

		// Now add field.
		acf_add_local_field( $field );

	}



	/**
	 * Add "Link" Field.
	 *
	 * @since 0.1
	 */
	public function field_link_add() {

		// Define field.
		$field = [
			'key' => 'field_idocs_bib_link',
			'label' => __( 'Link', 'idocs-bibliography' ),
			'name' => 'link',
			'type' => 'url',
			'instructions' => '',
			'default_value' => '',
			'placeholder' => '',
			'parent' => 'group_idocs_bib_data',
		];

		// Now add field.
		acf_add_local_field( $field );

	}



} // class iDocs_Bibliography_Metaboxes ends



