<?php

/**
 * Mailing List Plugin, Furasta.Org
 *
 * @author	Conor Mac Aoidh <conormacaoidh@gmail.com>
 * @licence	http://furasta.org/licence.txt The BSD Licence
 * @version	1
 */

if( !defined( 'AJAX_LOADED' ) || !defined( 'AJAX_VERIFIED' ) )
	exit;

$name = addslashes( @$_POST[ 'name' ] );
$email = addslashes( @$_POST[ 'email' ] );

if( empty( $email ) )
	die ( 'email' );

query( 'insert into ' . PREFIX . 'mailing_list values ( "", "' . $name . '", "' . $email . '")' );

echo mysql_insert_id( );

exit;
?>
