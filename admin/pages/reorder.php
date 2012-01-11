<?php

/**
 * Reorder, Furasta.Org
 *
 * Accessed via AJAX, this file reorders pages
 * according to data sent via POST.
 *
 * @author     Conor Mac Aoidh <conormacaoidh@gmail.com>
 * @license    http://furasta.org/licence.txt The BSD License
 * @version    1.0
 * @package    admin_pages
 */

/**
 * make sure ajax script was loaded and user is
 * logged in 
 */
if( !defined( 'AJAX_LOADED' ) || !defined( 'AJAX_VERIFIED' ) )
        die( );

$pages = addslashes_array( $_POST[ 'node' ] );
foreach( $pages as $position => $page ){
	query( 'update ' . PAGES . ' set position=' . $position . ', parent=' . $page[ 'parent' ] . ' where id=' . $page[ 'id' ] );
}

exit;

?>
