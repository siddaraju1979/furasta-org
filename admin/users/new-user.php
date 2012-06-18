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

// load users functions
require HOME . '_inc/function/users.php';

$name = addslashes( @$_POST[ 'name' ] );
$email = addslashes( @$_POST[ 'email' ] );
$password = @$_POST[ 'password' ];
$repeat = @$_POST[ 'repeat' ];
$groups = explode( ',', addslashes( @$_POST[ 'groups' ] ) );

/**
 * validate post info 
 */
if( empty( $name ) || empty( $email ) || empty( $password ) || empty( $groups ) )
	die( 'error' );

if( !filter_var( $email, FILTER_VALIDATE_EMAIL ) )
	die( 'error' );

if( $password != $repeat )
	die( 'error' );

$id = user_create( $name, $email, $password, $groups ); 
if( !$id )
	die( 'error' );

die( print( $id ) );
?>
