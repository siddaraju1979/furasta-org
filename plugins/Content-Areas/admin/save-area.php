<?php

/**
 * Content Areas Plugin, Furasta.Org
 *
 * @author	Conor Mac Aoidh <conormacaoidh@gmail.com>
 * @licence	http://furasta.org/licence.txt The BSD Licence
 * @version	1
 */

if( !defined( 'AJAX_LOADED' ) || !defined( 'AJAX_VERIFIED' ) )
	exit;

$name = addslashes( @$_POST[ 'name' ] );
$content = @$_POST[ 'content' ];

if( $name == '' || $content == '' )
	exit;

$ContentAreas = ContentAreas::getInstance( );
$ContentAreas->addArea( $name, 'all', $content ); 

exit;
?>
