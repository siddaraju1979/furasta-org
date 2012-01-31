<?php

/**
 * New Group, Furasta.Org
 *
 * A facility to create a new group. Accessed
 * via AJAX.
 *
 * @author     Conor Mac Aoidh <conormacaoidh@gmail.com>
 * @license    http://furasta.org/licence.txt The BSD License
 * @version    1.0
 * @package    admin_users
 */

/**
 * make sure ajax script was loaded and user is
 * logged in 
 */
if( !defined( 'AJAX_LOADED' ) || !defined( 'AJAX_VERIFIED' ) )
        die( );

$name = addslashes( @$_POST[ 'name' ] );
$perms = addslashes( @$_POST[ 'perms' ] );

/**
 * validate post info 
 */
if( empty( $name ) )
	die( 'error' );

/**
 * check if group name is used already 
 */
if( num( 'select name from ' . GROUPS . ' where name="' . $name .'"' ) != 0 )
	die( 'error' ); 

/**
 * add group to database 
 */
mysql_query( 'insert into ' . GROUPS . ' values( "", "' . $name . '", "' . $perms . '" )' );
$id = mysql_insert_id( );

// make group dirs
$public = PUBLIC_FILES . 'groups/' . $id;
if( !is_dir( $public ) )
	mkdir( $public );
$private = PRIVATE_FILES . 'groups/' . $id;
if( !is_dir( $private ) )
	mkdir( $private );

// clear caches
cache_clear( 'USERS' );

die( print( $id ) );
?>
