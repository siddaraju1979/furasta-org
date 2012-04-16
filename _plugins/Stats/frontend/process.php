<?php

/**
 * Stats Plugin, Furasta.Org
 *
 * @author	Conor Mac Aoidh <conormacaoidh@gmail.com>
 * @version	1
 */

/**
 * if not loaded via ajax script, exit or if logged in, exit
 */
if( !defined( 'AJAX_LOADED' ) || defined( 'AJAX_VERIFIED' ) )
	exit;

// load user agent script
require HOME . '_plugins/Stats/frontend/UserAgent.php';
$UserAgent = new UserAgent( );
$data = $UserAgent->detect( HOME . '_plugins/Stats/frontend/ua.xml' );

$page = addslashes( @$_POST[ 'page' ] );
$referrer = addslashes( @$_POST[ 'referrer' ] );
$ip = addslashes( $_SERVER[ 'REMOTE_ADDR' ] );
$browser = empty( $data[ 'browser' ] ) ? 'other' : addslashes( $data[ 'browser' ] );
$lang = empty( $data[ 'lang' ] ) ? 'other' : addslashes( $data[ 'lang' ] );
$os = empty( $data[ 'os' ] ) ? 'other' : addslashes( $data[ 'os' ] );
$country = 'unknown';

// add info to db
query( 'insert into ' . PREFIX . 'stats values(
	"",
	"' . $ip . '",
	"' . $page . '",
	"' . $referrer . '",
	"' . $os . '",
	"' . $browser . '",
	"' . $lang . '",
	"' . $country . '",
	now( ) 
)' );

exit;
?>
