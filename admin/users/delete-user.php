<?php

/**
 * Delete User, Furasta.Org
 *
 * Deletes the user.
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
	exit;

$user=row('select name,email from '.USERS.' where id='.$id);

$subject='User Suspended | Furasta.Org';
$message=$user['name'].',

	Your user account at '. $SETTINGS[ 'site_title' ] . ' ( ' . SITEURL .' ) has been suspended by another user. You will no longer be able to login to this website or perform any privileged actions.

	If you are not the person stated above please ignore this email.

	Thanks
	---
	Furasta.Org
	http://furasta.org
	support@furasta.org
        ';
        $headers='From: support@furasta.org'."\r\n".'Reply-To: support@furasta.org'."\r\n".'X-Mailer: PHP/' .phpversion();
        mail($user['email'],$subject,$message,$headers);

// delete user dir
User::removeUserDirs( $id );

query('delete from '.USERS.' where id='.$id);

cache_clear( 'USERS' );
?>
