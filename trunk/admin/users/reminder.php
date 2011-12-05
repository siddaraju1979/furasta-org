<?php

/**
 * Password Reminder, Furasta.Org
 *
 * Verifies the users email and sends an email with
 * password reset details to the user.
 *
 * @author     Conor Mac Aoidh <conormacaoidh@gmail.com>
 * @license    http://furasta.org/licence.txt The BSD License
 * @version    1.0
 * @package    admin_users
 */

/**
 * make sure file was ajax loaded 
 */
if( !defined( 'AJAX_LOADED' ) )
	exit;

/**
 * add libraries 
 */
require HOME . '_inc/function/system.php';
require HOME . '_inc/function/db.php';

$email = addslashes( @$_GET[ 'email' ] );

$rows = rows( 'select id,name from ' . USERS . ' where email="' . $email . '"' );
$num = count( $rows );

if( $num != 1 )
	die( '1' );

$hash = md5( mt_rand( ) );

query( 'update ' . USERS . ' set reminder="' . $hash . '" where id="' . $rows[ 0 ][ 'name' ] . '"');

$message = 'Hi ' . $rows[ 0 ][ 'name' ] . '<br/>
You have requested a new password. Please follow the link below to reset your password:<br/>
' . SITEURL . 'admin/users/reset_password.php?reminder=' . $hash . '<br/>
If you have not requested a new password please ignore this email.<br/>
';

/**
 * email password reset details 
 */
email( $email, 'Password Reset - Furasta.Org', $message ); 

die( '0' );
?>
