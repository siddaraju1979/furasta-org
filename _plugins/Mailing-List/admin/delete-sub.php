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

$id = (int) @$_POST[ 'id' ];

if( empty( $id ) )
	exit;

query( 'delete from ' . DB_PREFIX . 'mailing_list where id=' . $id );
?>
