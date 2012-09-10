<?php

/**
 * Install Completion, Furasta.Org
 *
 * Completes the install process. Installs the database,
 * creates the settings.php file and emails the user
 * with a validation link.
 *
 * @author     Conor Mac Aoidh <conormacaoidh@gmail.com>
 * @license    http://furasta.org/licence.txt The BSD License
 * @version    1.0
 * @package    installer
 */

session_start( );

if(@$_SESSION['complete']!=1)
	header('location: stage3.php');

require 'header.php';
require HOME . '_inc/function/system.php'; // this should be removed and all required functionality moved to defaults.php
require HOME . '_inc/function/defaults.php';

$connect=mysql_connect($_SESSION['db']['host'],$_SESSION['db']['user'],$_SESSION['db']['pass']);
$select=mysql_select_db($_SESSION['db']['name'],$connect);

$prefix=$_SESSION['db']['prefix'];
$hash=md5(mt_rand());
$site_url=$_SESSION['settings']['site_url'];

// constants for .settings.php
$constants = array(
        'PAGES'                 => $prefix . 'pages',
        'USERS'                 => $prefix . 'users',
        'TRASH'                 => $prefix . 'trash',
        'GROUPS'                => $prefix . 'groups',
        'USERS_GROUPS'          => $prefix . 'users_groups',
        'OPTIONS'               => $prefix . 'options',
        'FILES'                 => $prefix . 'files',
        'TEMPLATE_DIR'          => HOME . '_www/vitruvius',     // @todo update
        'PREFIX'                => $prefix,
        'VERSION'               => '0.9.2',                     // @todo define version in install header
        'SITEURL'               => $site_url,
        'USER_FILES'            => $_SESSION[ 'settings' ][ 'user_files' ],
        'DIAGNOSTIC_MODE'       => 0,
        'LANG'                  => 'English',
        'SET_REVISION'          => 100
);
$constants = defaults_constants( $constants );

// no plugins installed by default
$plugins = array( );

/*
 * settings array for .settings.php
 */
$settings = array(
        'site_title'            => addslashes( $_SESSION[ 'settings' ][ 'title' ],
        'site_subtitle'         => addslashes( $_SESSION[ 'settings' ][ 'sub_title' ],
        'index'                 => ( int ) $_SESSION[ 'settings' ][ 'index' ],
        'maintenance'           => $_SESSION[ 'settings' ][ 'maintenance' ],
        'system_alert'          => ''
);

$db = array(
        'name' => addslashes( $_SESSION[ 'db' ][ 'name' ] ),
        'host' => addslashes( $_SESSION[ 'db' ][ 'host' ] ),
        'user' => addslashes( $_SESSION[ 'db' ][ 'user' ] ),
        'pass' => addslashes( $_SESSION[ 'db' ][ 'pass' ] )
);

$filecontents = defaults_settings_content( $settings, $db, $plugins, $constants );


/**
 * write settings file
 */
file_put_contents(HOME.'.settings.php',$filecontents) or error('Please grant <i>0777</i> write access to the <i>'.HOME.'</i> directory then reload this page to complete installation.');


install_db( $settings, $constants );

// dirs to create
$dirs = array(
	$user_files,
	// system dirs
	$user_files . 'cache',
	$user_files . 'backup',
	$user_files . 'smarty',
	$user_files . 'smarty/templates',
	$user_files . 'smarty/templates_c',
	
	// file manager dirs
	$user_files . 'files',
	$user_files . 'files/users',
	$user_files . 'files/users/' . $user_id,
	$user_files . 'files/groups',
	$user_files . 'files/groups/_superuser'
);

// create dirs
$match = true;
foreach( $dirs as $dir ){
	if( !is_dir( $dir ) )
		mkdir( $dir ) || $match = false;
}
if( $match == false )
	die( 'could not create some of the required directories in the user dir. make sure your user dir is writable' );

$rules = defaults_htaccess_rules( $site_url );

$htaccess = defaults_htaccess_content( $rules );

file_put_contents(HOME.'.htaccess',$htaccess);


$robots = defaults_robots_content( $index, $site_url );

file_put_contents(HOME.'robots.txt',$robots);

$url='http://'.$_SERVER['SERVER_NAME'];
$subject='User Activation - Furasta.Org';
$message='Hi ' . $_SESSION['user']['name'].',<br/>
	<br/>
	Please activate your new user by clicking on the link below:<br/>
	<br/>
	<a href="'.$url.'/admin/users/activate.php?hash='.$hash.'">'.$url.'/admin/users/activate.php?hash='.$hash.'</a><br/>
	<br/>
	If you are not the person stated above please ignore this email.<br/>
';
email( $_SESSION['user']['email'], $subject, $message );

echo '
<h1>Complete! Verification Email Sent to ' . $_SESSION[ 'user' ][ 'email' ] . '</h1>
<p>Your Furasta CMS installation has been performed successfully. A user verification email has been sent to your email address. You must verify this address before you can log in. Please now finish and configure your website.</p>
<br/>
<a href="../admin/index.php" class="grey-submit right">Finish</a>
</form>
<br style="clear:both"/>
';

require 'footer.php';
?>
