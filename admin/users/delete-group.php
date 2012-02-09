<?php

/**
 * Delete Group, Furasta.Org
 *
 * Deletes a group.
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

$id= addslashes( @$_GET['id'] );
if($id=='')
	die( );

$users = rows( 'select * from ' . USERS . ' where user_group="' . $id . '"' );

foreach( $users as $user ){

	$subject='User Suspended | Furasta.Org';
	$message=$user['name'].',

        Your user account at '. $SETTINGS[ 'site_title' ] . ' ( ' . SITEURL .' ) has been suspended by another user. You will no longer be able to login to this website or perform any privileged actions.

        If you are not the person stated above please ignore this email.';
        email( $user['email'], $subject, $message );


}

// delete user / group dirs
$users = rows( 'select id from ' . USERS . ' where user_group="' . $id . '"' );
foreach( $users as $user ){
	User::removeUserDirs( $user[ 'id' ] );
}

Group::removeGroupDirs( $id );

// delete user / group
query( 'delete from ' . USERS . ' where user_group="' . $id . '"' );
query( 'delete from ' . GROUPS . ' where id=' . $id );

cache_clear( 'USERS' );
?>
