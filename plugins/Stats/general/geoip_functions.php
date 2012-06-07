<?php

/**
 * Stats Plugin, Furasta.Org
 *
 * @author	Conor Mac Aoidh <conormacaoidh@gmail.com>
 * @version	1
 */

/**
 * stats_increment
 *
 * increments a number no matter how large
 * example:
 * 800 -> 900
 * 40000000 -> 50000000
 * 3 -> 4
 *
 * @params int $num
 * @return int
 */
function stats_increment( $num, $len ){
	$d = $len - 1;
	for( $i = 0, $n = substr( $num, 0, 1 ) + 1; $i < $d; $n *= 10, ++$i );
	return $n;	
}

function stats_split_ip_range( $start, $end ){
	$slen = strlen( $start );
	$elen = strlen( $end );
	$len = ( $slen < $elen ) ? $elen : $slen;
	$up = stats_increment( $start, $len );
	$ips = array( );

	if( $up < $end )
		$ips += stats_split_ip_range( $up, $end );
	else
		$up = $end;

	array_push( $ips, array(
		$start, ( $up - 1 )
	) );	

	return $ips;

}

$ips = stats_split_ip_range( 100000, 199999 );
//$ips = stats_split_ip_range( substr( $ips[ 0 ][ 0 ], 1 ), substr( $ips[ 0 ][ 1 ], 1 ) );
die( print_r( $ips ) );

?>
