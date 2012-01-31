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
$public = PUBLIC_FILES . 'users/' . $id;
if( !is_dir( $public ) )
	mkdir( $public );
$private = PRIVATE_FILES . 'users/' . $id;
if( !is_dir( $private ) )
	mkdir( $private );

/**
 * send email to user 
 */
$message = 'Hi ' . $name . '<br/>
You have requested a new password. Please follow the link below to reset your password:<br/>
' . SITEURL . '/admin/settings/users/activate.php?hash=' . $hash . '<br/>
If you have not requested a new password please ignore this email.<br/>
';

email( $email, 'Activation - Furasta.Org', $message );

cache_clear( 'USERS' );

die( print( $id ) );
?>
