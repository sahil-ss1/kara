<?php

/*
 * Example PHP implementation used for the index.html example
 */

// DataTables PHP library
include( "../lib/DataTables.php" );

// Alias Editor classes so they are easy to use
use
	DataTables\Editor,
	DataTables\Editor\Field,
	DataTables\Editor\Format,
	DataTables\Editor\Mjoin,
	DataTables\Editor\Options,
	DataTables\Editor\Upload,
	DataTables\Editor\Validate,
    DataTables\Editor\ValidateOptions,
    DataTables\Editor\SearchPaneOptions;

// Build our Editor instance and process the data coming from _POST
Editor::inst( $db, 'datatables_demo' )
	->fields(
        Field::inst( 'first_name' )
            ->searchPaneOptions( SearchPaneOptions::inst())
			->validator( Validate::notEmpty( ValidateOptions::inst()
				->message( 'A first name is required' )	
			) ),
        Field::inst( 'last_name' )
            ->searchPaneOptions( SearchPaneOptions::inst())
			->validator( Validate::notEmpty( ValidateOptions::inst()
				->message( 'A last name is required' )	
			) ),
        Field::inst( 'position' )
            ->searchPaneOptions( SearchPaneOptions::inst()),
        Field::inst( 'email' )
            ->searchPaneOptions( SearchPaneOptions::inst())
			->validator( Validate::email( ValidateOptions::inst()
				->message( 'Please enter an e-mail address' )	
			) ),
        Field::inst( 'office' )
            ->searchPaneOptions( SearchPaneOptions::inst()),
        Field::inst( 'extn' )
            ->searchPaneOptions( SearchPaneOptions::inst()),
        Field::inst( 'age' )
            ->searchPaneOptions( SearchPaneOptions::inst())
			->validator( Validate::numeric() )
			->setFormatter( Format::ifEmpty(null) ),
        Field::inst( 'salary' )
            ->searchPaneOptions( SearchPaneOptions::inst())
			->validator( Validate::numeric() )
			->setFormatter( Format::ifEmpty(null) ),
        Field::inst( 'start_date' )
            ->searchPaneOptions( SearchPaneOptions::inst())
			->validator( Validate::dateFormat( 'Y-m-d' ) )
			->getFormatter( Format::dateSqlToFormat( 'Y-m-d' ) )
			->setFormatter( Format::dateFormatToSql('Y-m-d' ) )
	)
	->process( $_POST )
	->json();
