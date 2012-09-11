<?php

/**
 * Verify User Email, Furasta.Org
 *
 * Verifies the users email and sends an email with
 * password reset details to the user.
 *
 * @author     Conor Mac Aoidh <conormacaoidh@gmail.com>
 * @license    http://furasta.org/licence.txt The BSD License
 * @version    1.0
 * @package    admin_users
 */

$email=addslashes(@$_GET['email']);
if($email=='')
	die('1');


$query=mysql_query('select * from '.DB_USERS.' where email="'.$email.'"');
$num=mysql_num_rows($query);

if($num==1)
	die('0');
else{
	$hash=md5(mt_rand());
	query('update '.DB_USERS.' set reminder="'.$hash.'" where email="'.$email.'"');
	$name = single( 'select name from ' . DB_USERS . ' where email="' . $email . '" and hash="' . $hash . '"', 'name' );
	$subject='Password Reminder | Furasta.Org';
	$message=
		$name . ',
		<br/>
		<p>You have requested a new password. Please follow the link below to reset your password:</p>
		<br/>
		<p>'. SITE_URL .'/admin/settings/users/reset_password.php?reminder='.$hash.'</p>
		<br/>
		<p>If you have not requested a new password please ignore this email.</p>
	';
	email($email,$subject,$message);
	die('1');
}
?>
