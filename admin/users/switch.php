<?php

/**
 * Switch Users, Furasta.Org
 *
 * Allows the super user to switch to other logins
 *
 * @author     Conor Mac Aoidh <conormacaoidh@gmail.com>
 * @license    http://furasta.org/licence.txt The BSD License
 * @version    1.0
 * @package    admin_users
 */

/**
 * make sure ajax loaded and is super user
 */
if( !defined( 'AJAX_LOADED' ) || !defined( 'AJAX_VERIFIED' ) || $User->groupName( ) != '_superuser' )
	die( 'nope' );

/**
 * login as other user
 */
$userid = addslashes( $_POST[ 'userid' ] );
$hash = addslashes( $_POST[ 'hash' ] );

$User = User::loginHash( $userid, $hash, array( 'a' ), true );

if( !$User ){
	$Template = Template::getInstance( );
	die( $Template->displayErrors( ) );
}

/**
 * redirect to admin overview area
 */
header( 'location: ' . SITEURL . 'admin/index.php' );
?>
