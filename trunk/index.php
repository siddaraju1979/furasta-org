<?php

/**
 * Index File, Furasta.Org
 *
 * This is the first file loaded in the forntend of the CMS. It
 * figures out what page has been accessed and then includes
 * a file to deal with the Smarty Template Engine.
 *
 * @author     Conor Mac Aoidh <conormacaoidh@gmail.com>
 * @license    http://furasta.org/licence.txt The BSD License
 * @version    1.0
 * @package    admin_overview
 */

ob_start('ob_gzhandler');
header('Connection: close');
header('Content-type: text/html; charset: UTF-8');

require '_inc/define.php';
require_once $function_dir . 'frontend.php';

/**
 * execute onload plugin functions
 */
$Plugins->hook( 'frontend', 'on_load' );

/**
 * if mantinence mode is enabled and user not
 * logged in then display mantinence error
 * @todo enable custom mantinence error and option to use
 * mantinence template 
 */
$User = User::getInstance( );
$login = User::verify( );
if( $SETTINGS[ 'maintenance' ] == 1 && !$login )
	maintenance_message( );

$page=@$_GET['page'];

if( empty( $page ) )
	$id = single( 'select id from ' . PAGES . ' where home=1 limit 1', 'id' );
else{
        $array=explode('/',$page);
        if(end($array)=='')
                array_pop($array);
        $slug=addslashes(end($array));
	$id=single('select id from '.PAGES.' where slug="'.$slug.'"','id' );
	if(!$id)
		frontend_error( '<h1>404 - File Not Found</h1><p>The page your are looking for does not '
		 . 'exist</p><p><a href="' . SITEURL . '">Site Index</a></p>', '404 - File Not Found' );
}

/**
 * get page rows in array and stripslashes 
 */
$Page = row( 'select * from ' . PAGES . ' where id=' . $id, true );
$Page = stripslashes_array( $Page );

// make sure user has permission to view page
$perm = explode( '|', $Page[ 'perm' ] );
if( $User && !$User->frontendPagePerm( $perm[ 0 ] ) )
	frontend_error( '<h1>Permissions Error</h1><p>You have insufficient privellages to view this page.</p>',
	 'Permissions Error' );

require HOME.'_inc/smarty.php';

/**
 * show output, cancel request connection
 * and execute plugin
 */
ignore_user_abort( true );
ob_end_flush();
flush();

// execute plugin on_finish stuff
$Plugins->hook( 'frontend', 'on_finish' );
?>
