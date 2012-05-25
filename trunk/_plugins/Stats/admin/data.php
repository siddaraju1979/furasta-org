<?php

/**
 * Stats Plugin, Furasta.Org
 *
 * @author	Conor Mac Aoidh <conormacaoidh@gmail.com>
 * @version	1
 */

if( !defined( 'AJAX_LOADED' ) || !defined( 'AJAX_VERIFIED' ) )
	exit;

$query = @$_GET[ 'query' ];

function stats_resort( $arr ){
	$sorted = array( );
	foreach( $arr as $ar ){
		
	}
}

switch( $query ){
	case 'today-total':
		$data = array( );
		$views = rows(
			'select ip,day(viewed) from ' . PREFIX .
			'stats where day(viewed)>(day(now())-10) and month(viewed)=month(now()) and year(viewed)=year(now())'
		);
		$unique = rows(
			'select distinct ip,day(viewed) from ' . PREFIX .
			'stats where day(viewed)>(day(now())-10) and month(viewed)=month(now()) and year(viewed)=year(now())'
		);

		/**
		 * calculate dates
		 */
		$data[ 'categories' ] = array(
			date( 'D', strtotime( '-9 days' ) ),	
			date( 'D', strtotime( '-8 days' ) ),
			date( 'D', strtotime( '-7 days' ) ),
			date( 'D', strtotime( '-6 days' ) ),
			date( 'D', strtotime( '-5 days' ) ),
			date( 'D', strtotime( '-4 days' ) ),
			date( 'D', strtotime( '-3 days' ) ),
			date( 'D', strtotime( '-2 days' ) ),
			date( 'D', strtotime( '-1 day' ) ),
			date( 'D' )
		);


		$data[ 'views' ] = array(
			array( 'name' => 'Total Views', 'data' => array( 0,0,0,0,0,0,0,0,0,0 ) ),
			array( 'name' => 'Unique Views', 'data' => array( 0,0,0,0,0,0,0,0,0,0 ) )
		);

		$today = (int) date( 'd' );
		foreach( $views as $row ){
			$d = $today - $row[ 'day(viewed)' ];
			$data[ 'views' ][ 0 ][ 'data' ][ $d ]++;
		}
		$data[ 'views' ][ 0 ][ 'data' ] = array_reverse( $data[ 'views' ][ 0 ][ 'data' ] );

		foreach( $unique as $row ){
			$d = $today - $row[ 'day(viewed)' ];
			$data[ 'views' ][ 1 ][ 'data' ][ $d ]++;
		}
		$data[ 'views' ][ 1 ][ 'data' ] = array_reverse( $data[ 'views' ][ 1 ][ 'data' ] );

		echo json_encode( $data );
	break;
}

exit;
?>
