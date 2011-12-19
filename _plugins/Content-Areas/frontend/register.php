<?php

/**
 * Content Areas Plugin, Furasta.Org
 *
 * @author      Conor Mac Aoidh <conormacaoidh@gmail.com>
 * @licence     http://furasta.org/licence.txt The BSD Licence
 * @version     1
 * @todo add permissions here
 */

// make sure configs were loaded and user is logged in
if( !defined( 'AJAX_LOADED' ) || !defined( 'AJAX_VERIFIED' ) )
	exit;


$name = addslashes( @$_POST[ 'name' ] );
$page_id = addslashes( @$_POST[ 'id' ] );
$data = array( 
	'width' => @$_POST[ 'width' ],
	'left' => @$_POST[ 'left' ],
	'top' => @$_POST[ 'top' ]
);

$ContentAreas = ContentAreas::getInstance( );
$ContentAreas->addArea( $name, $page_id, $data );
?>
