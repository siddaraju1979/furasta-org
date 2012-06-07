<?php

/**
 * Forum Plugin, Furasta.Org
 *
 * @author	Conor Mac Aoidh <conormacaoidh@gmail.com>
 * @licence	http://furasta.org/licence.txt The BSD Licence
 * @version	1
 */

if( !defined( 'AJAX_LOADED' ) || !defined( 'AJAX_VERIFIED' ) )
	exit;

$name = addslashes( @$_POST[ 'forum_name' ] );
$desc = addslashes( @$_POST[ 'forum_desc' ] );
$page_id = addslashes( @$_POST[ 'forum_id' ] );

if( empty( $name ) || empty( $page_id ) )
	exit;

query( 'insert into ' . PREFIX . 'forums values ( "", ' . $page_id . ', "' . $name . '", "' . $desc . '", "" )' );

echo mysql_insert_id( );
exit;
?>
