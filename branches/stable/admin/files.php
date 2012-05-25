<?php

/**
 * Files Switch, Furasta.Org
 *
 * Switches between possible files actions.
 *
 * @author     Conor Mac Aoidh <conormacaoidh@gmail.com>
 * @license    http://furasta.org/licence.txt The BSD License
 * @version    1.0
 * @package    admin_pages
 */

require 'header.php';

$action = addslashes( @$_GET[ 'action' ] );

switch( $action ){
	default:
		if( !$User->hasPerm( 'f' ) )
		        error( 'You have insufficient privelages to view this page. Please contact one of the administrators.', 'Permissions Error' );

		$Template->add('title','List Files');
		require 'files/list.php';
}

require 'footer.php';
?>
