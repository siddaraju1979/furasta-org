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

$area_name = addslashes( @$_POST[ 'name' ] );
$widget_id = (int) @$_POST[ 'id' ];

if( empty( $area_name ) || empty( $widget_id ) )
	exit;

$ContentAreas = ContentAreas::getInstance( );
$ContentAreas->deleteWidget( $area_name, $widget_id );

exit;
?>
