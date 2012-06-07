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

$widget_name = addslashes( @$_POST[ 'name' ] );
$widget_id = ( int ) @$_POST[ 'id' ];
$area_name = addslashes( @$_POST[ 'area_name' ] );

if( empty( $widget_name ) || empty( $area_name ) )
	exit;

$ContentAreas = ContentAreas::getInstance( );

$widget = $ContentAreas->widgets( $widget_name );

$new_id = call_user_func_array( $widget[ 'admin' ], array( $area_name, $widget_id, 'all' ) ); 

// if just looking for a new id then print it and exit
if( $widget_id == 0 ){
	echo $new_id;
	exit;
}

$Template = Template::getInstance( );
require HOME . 'admin/layout/ajax.php';

exit;
?>
