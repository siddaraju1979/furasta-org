<?php

/**
 * Plugins Admin Page, Furasta.Org
 *
 * Allows plugins to create admin pages.
 *
 * @author     Conor Mac Aoidh <conormacaoidh@gmail.com>
 * @license    http://furasta.org/licence.txt The BSD License
 * @version    1.0
 * @package    plugin_architecture
 */

require 'header.php';

/**
 * check if user has permission to view page 
 */
if( !$User->hasPerm( 'p' ) )
        error( 'You have insufficient privelages to view this page. Please contact one of the administrators.', 'Permissions Error' );

$p_name=str_replace('-',' ',@$_GET['p_name']);

if($p_name=='')
	error('Undefined plugin error, please de-activate recently activated plugins to resolve the problem.','Plugin Error');

$Plugins->adminPage( $p_name );

/**
 * allows plugins to have their own templates for plugin
 * pages
 */
$plugin = $Plugins->plugins( $p_name );
if( isset( $plugin[ 'admin' ][ 'template_override' ] ) ){
	$template = $plugin[ 'admin' ][ 'template_override' ];
	if( validate_file( $template ) ){
		require HOME . $template;
		exit;
	}
}

require HOME . 'admin/layout/admin.php';
 
?>
