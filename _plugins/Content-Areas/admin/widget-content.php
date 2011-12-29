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

if( empty( $widget_name ) || empty( $widget_id ) || empty( $area_name ) )
	exit;

$ContentAreas = ContentAreas::getInstance( );

$widget = $ContentAreas->widgets( $widget_name );

call_user_func_array( $widget[ 'admin' ], array( $area_name, $widget_id, 'all' ) ); 

$Template = Template::getInstance( );
require HOME . 'admin/layout/ajax.php';

exit;
?>
