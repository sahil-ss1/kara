<?php

include("../lib/DataTables.php");

use
    DataTables\Editor,
    DataTables\Editor\Field,
    DataTables\Editor\SearchPaneOptions;

Editor::inst( $db, 'datatables_demo' )
    ->fields(
        Field::inst( 'first_name' )
            ->searchPaneOptions(SearchPaneOptions::inst()),
        Field::inst( 'last_name' )
            ->searchPaneOptions(SearchPaneOptions::inst()),
        Field::inst( 'position' )
            ->searchPaneOptions(SearchPaneOptions::inst()),
        Field::inst( 'office' )
            ->searchPaneOptions(SearchPaneOptions::inst()),
        Field::inst( 'extn' ),
        Field::inst( 'start_date' ),
        Field::inst( 'salary' )
    )
    ->write(false)
    ->process($_POST)
    ->json();