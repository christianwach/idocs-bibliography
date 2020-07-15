<?php

/**
 * iDocs Bibliography Theme functions.
 *
 * Global scope functions that are available to the theme can be found here.
 *
 * @package iDocs_Bibliography
 */



/**
 * Show a citation.
 *
 * @since 0.1
 */
function idocs_the_citation() {
	echo idocs_get_the_citation();
}



/**
 * Build a citation.
 *
 * @since 0.1
 *
 * @return $citation The built citation markup.
 */
function idocs_get_the_citation() {

	// Init return.
	$citation = '';

	// Build author markup.
	$author_markup = idocs_get_the_citation_author();

	// Get the date published.
	$published = get_field( 'field_idocs_bib_year' );

	// Wrap year in brackets if we get one.
	$year = '';
	if ( ! empty( $published ) ) {
		$year = '(' . $published . ') ';
	}

	// Get the title.
	$title = get_the_title();

	// Wrap in quotes if we get one.
	if ( ! empty( $title ) ) {
		$title = '&ldquo;' . $title . '&rdquo;. ';
	}

	// Get publication.
	$publication = idocs_get_the_publication();
	if ( ! empty( $publication ) ) {
		$publication .= ' ';
	}

	// Get publisher.
	$publisher = idocs_get_the_publisher();
	if ( ! empty( $publisher ) ) {
		$publisher .= ' ';
	}

	// Build ISBN.
	$isbn = get_field( 'field_idocs_bib_isbn' );
	if ( ! empty( $isbn ) ) {
		$isbn = sprintf( __( 'ISBN %s', 'idocs-bibliography' ), $isbn );
		$isbn .= ' ';
	}

	// Build DOI.
	$doi = get_field( 'field_idocs_bib_doi' );
	if ( ! empty( $doi ) ) {
		$doi = sprintf( __( 'DOI %s', 'idocs-bibliography' ), $doi );
		$doi .= ' ';
	}

	// Build citation.
	$citation = trim( $author_markup . $year . $title . $publication . $publisher . $isbn. $doi );

	// --<
	return $citation;

}



/**
 * Show a citation author.
 *
 * @since 0.2
 */
function idocs_the_citation_author() {
	echo idocs_get_the_citation_author();
}



/**
 * Build a citation author.
 *
 * @since 0.2
 *
 * @return $markup The built author markup.
 */
function idocs_get_the_citation_author() {

	// Get repeating authors fields.
	$authors = get_field( 'field_idocs_bib_authors' );

	// Build author markup.
	$author_markup = '';
	if ( ! empty( $authors ) ) {
		$build = [];
		foreach( $authors AS $author ) {
			$build[] = $author['author'];
		}
		$author_markup = implode( '; ', $build ) . ' ';
	}

	// --<
	return $author_markup;

}



/**
 * Show a citation link.
 *
 * @since 0.1
 */
function idocs_the_citation_link() {
	echo idocs_get_the_citation_link();
}



/**
 * Build a citation link.
 *
 * @since 0.1
 *
 * @return $citation The built citation markup.
 */
function idocs_get_the_citation_link() {

	// Init return.
	$link = '';

	// Build link.
	$url = get_field( 'field_idocs_bib_link' );
	if ( ! empty( $url ) ) {
		$link = '<a href="' . $url . '">' . esc_html__( 'Access Resource', 'idocs-bibliography' ) . '</a>';
	}

	// --<
	return $link;

}



/**
 * Build a publication.
 *
 * @since 0.1
 *
 * @return $markup The built publication markup.
 */
function idocs_get_the_publication() {

	// Init return.
	$markup = '';

	// Get publication.
	$publication = get_field( 'field_idocs_bib_publication' );

	// Bail if empty.
	if ( empty( $publication ) ) {
		return $markup;
	}

	// Build parts.
	$publication .= ' ';

	// Build volume.
	$volume = get_field( 'field_idocs_bib_volume' );
	if ( ! empty( $volume ) ) {
		$volume = sprintf( __( 'Vol %s ', 'idocs-bibliography' ), $volume );
	}

	// Build issue.
	$issue = get_field( 'field_idocs_bib_issue' );
	if ( ! empty( $issue ) ) {
		$issue = sprintf( __( 'Issue %s, ', 'idocs-bibliography' ), $issue );
	}

	// Build from-to.
	$pp = '';
	$page_from = get_field( 'field_idocs_bib_page_from' );
	$page_to = get_field( 'field_idocs_bib_page_to' );
	if ( ! empty( $page_from ) AND ! empty( $page_to ) ) {
		$pp = sprintf( __( 'Pp. %1$s&mdash;%2$s, ', 'idocs-bibliography' ), $page_from, $page_to );
	} elseif ( ! empty( $page_from ) AND empty( $page_to ) ) {
		$pp = sprintf( __( 'Pp. %s, ', 'idocs-bibliography' ), $page_from );
	}

	// Build place.
	$place = get_field( 'field_idocs_bib_place' );
	if ( ! empty( $place ) ) {
		$place .= ', ';
	}

	// Get publisher.
	$publisher = get_field( 'field_idocs_bib_publisher' );

	// Build parts.
	$parts = $publication . $volume . $issue . $pp . $place . $publisher;

	// Build markup.
	$markup = sprintf( __( 'In: %s', 'idocs-bibliography' ), $parts );

	// --<
	return $markup;

}



/**
 * Build a publisher.
 *
 * @since 0.1
 *
 * @return $markup The built publisher markup.
 */
function idocs_get_the_publisher() {

	// Init return.
	$markup = '';

	// Get publication.
	$publication = get_field( 'field_idocs_bib_publication' );

	// Bail if publiccation is defined.
	if ( ! empty( $publication ) ) {
		return $markup;
	}

	// Build place.
	$place = get_field( 'field_idocs_bib_place' );
	if ( ! empty( $place ) ) {
		$place .= ', ';
	}

	// Get publisher.
	$publisher = get_field( 'field_idocs_bib_publisher' );

	// Build markup.
	$markup = $place . $publisher;

	// --<
	return $markup;

}



