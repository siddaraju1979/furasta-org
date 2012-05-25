<?php

/**
 * New User, Furasta.Org
 *
 * A facility to create a new user. Accessed
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
$email = addslashes( @$_POST[ 'email' ] );
$password = @$_POST[ 'password' ];
$repeat = @$_POST[ 'repeat' ];
$group = addslashes( @$_POST[ 'group' ] );

/**
 * validate post info 
 */
if( empty( $name ) || empty( $email ) || empty( $password ) || empty( $group ) )
	die( 'error' );

if( !filter_var( $email, FILTER_VALIDATE_EMAIL ) )
	die( 'error' );

if( $password != $repeat )
	die( 'error' );

/**
 * check if email is used already 
 */
if( num( 'select id from ' . USERS . ' where email="' . $email .'"' ) != 0 )
	die( 'error' ); 

$hash = md5( mt_rand( ) );

/**
 * add user to database 
 */
mysql_query( 'insert into ' . USERS . ' values( "", "' . $name . '", "' . $email . '", "' . md5( $password ) . '", "' . $group . '", "' . $hash. '","", "" )' );
$id = mysql_insert_id( );

// add user dir to database
User::createUserDirs( $id );

/**
 * send email to user 
 */
$message=$name.',<br/>
	<br/>
	Please activate your new user by clicking on the link below:<br/>
	<br/>
	<a href="'.SITEURL.'admin/users/activate.php?hash='.$hash.'">'.$url.'/admin/users/activate.php?hash='.$hash.'</a><br/>
	<br/>
	If you are not the person stated above please ignore this email.<br/>
';
email( $email, 'User Activation - Furasta.Org', $message );

cache_clear( 'USERS' );

die( print( $id ) );
?>
