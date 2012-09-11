<?php

/**
 * AJAX Access, Furasta.Org
 *
 * Designed as a method through which you
 * can make AJAX requests through the CMS. This file
 * provides developers access to all main libraries that
 * the CMS provides, through AJAX, and then includes the
 * developers own script. It essentially provides a
 * working enviornment for AJAX calls.
 *
 * =========== USAGE ===========
 * 
 * Instead of making an ajax request to your file,
 * make it to the _inc/ajax.php file and pass your
 * filename as a url parameter:
 *
 * _inc/ajax.php?file=path/to/file
 *
 * THen you will have a fully set up furasta enviornment
 * in your file. It is recommended to use the AJAX_LOADED
 * constant to make sure the file was loaded through the
 * ajax file for security reasons:
 *
 * if( !defined( 'AJAX_LOADED' ) )
 * 	exit;
 *
 * The AJAX_VERIFIED constant can also be used to check
 * if the user is logged in:
 *
 * if( !defined( 'AJAX_LOADED' ) || !defined( 'AJAX_VERIFIED' ) )
 * 	exit;
 *
 * This file uses the Template class and the template located
 * at admin/layout/ajax.php. echo or print can still be used
 * as output is captured and added to the Template class.
 *
 * ========= DB_OPTIONS ==========
 * 
 * The no_config option can be used to load a minimal
 * enviornment with only the .settings.php file loaded
 * and a database connection setup:
 *
 * _inc/ajax.php?no_config&file=path/to/file
 * 
 * In this case you can use AJAX_NO_CONFIG to check
 * that the minimal enviornment was loaded
 *
 * @author     Conor Mac Aoidh <conormacaoidh@gmail.com>
 * @license    http://furasta.org/licence.txt The BSD License
 * @version    1.0
 */

/**
 * used to test is the ajax load script was exectued 
 */
define( 'AJAX_LOADED', true );

/**
 * checks if config and login are required
 */
if( isset( $_GET[ 'no_config' ] ) ){
	/**
	 * basic script setup - database connection,
	 * .settings contstants but no libraries 
	 */
	define( 'START_TIME', microtime( true ) );
	define( 'HOME', substr( dirname( __FILE__ ), 0, -4 ) );
	define( 'AJAX_NO_CONFIG', true );

	require HOME . '.settings.php';

	if( ! ( $connect = mysql_connect( $DB[ 'host' ], $DB[ 'user' ], $DB[ 'pass' ] ) ) )
        	die( 'The MySQL connection details are incorrect. The hostname, username or password are incorrect.' );

	if( ! mysql_select_db( $DB['name'], $connect ) )
        	die( 'Cannot connect to the MySQL database. Please make sure that the database name is correct.' );
}
else{
	/**
	 * normal setup script - database connection,
	 * .settings constants, libraries
	 * NB: define.php is run so plugins are loaded etc
	 *     also must be logged in 
	 */
	include 'define.php';

	/**
	 * if user is verified, define ajax verified
	 */
	$User = User::getInstance( );
	if( $User != false )
		define( 'AJAX_VERIFIED', true );
}


/**
 * make sure file is valid and exists
 */
$file = @$_GET[ 'file' ];

if( strpos( $file, '..' ) !== false )
	die( 'not a valid filename' );

$file = HOME . $file;

if( !file_exists( $file ) )
	die( 'The file at <i>' . $file . '</i> does not exist.' );

$Template = Template::getInstance( );

/**
 * capture output and add to template class
 */
ob_start( );
require $file;
$content = ob_get_contents( );
$Template->add( 'content', $content );
ob_end_clean( );

/**
 * display through admin/layout/ajax.php
 */
require HOME . 'admin/layout/ajax.php';

exit;
?>
