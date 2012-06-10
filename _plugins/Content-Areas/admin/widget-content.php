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
$postdata = ( empty( $_POST[ 'data' ] ) ) ? '' : $_POST[ 'data' ];

if( empty( $widget_name ) || empty( $area_name ) )
	exit;

$ContentAreas = ContentAreas::getInstance( );

$widget = $ContentAreas->widgets( $widget_name );

/**
 * reorder array to normal php post
 * array format
 */
if( is_array( $postdata ) ){
	$pdata = array( );

	foreach( $postdata as $p ){
		if( isset( $pdata[ $p[ 'name' ] ] ) ){
			if( is_array( $pdata[ $p[ 'name' ] ] ) )
				array_push( $pdata[ $p[ 'name' ] ], $p[ 'value' ] );
			else{
				$pdata[ $p[ 'name' ] ] = array( 
					$pdata[ $p[ 'name' ] ],
					$p[ 'value' ]
				);
			}
		}
		else
			$pdata[ $p[ 'name' ] ] = $p[ 'value' ];
	}
	$postdata = $pdata;
}

$new_id = call_user_func_array( $widget[ 'admin' ], array( $area_name, $widget_id, 'all', $postdata ) ); 

// if just looking for a new id then print it and exit
if( $widget_id == 0 ){
	echo $new_id;
	exit;
}

$Template = Template::getInstance( );
require HOME . 'admin/layout/ajax.php';

exit;
?>
