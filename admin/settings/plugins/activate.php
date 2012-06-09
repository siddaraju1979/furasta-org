<?php

/**
 * Activate Plugin, Furasta.Org
 *
 * Activates a plugin. This page is accessed via AJAX.
 *
 * @author     Conor Mac Aoidh <conormacaoidh@gmail.com>
 * @license    http://furasta.org/licence.txt The BSD License
 * @version    1.0
 * @package    admin_settings
 */

$p_name = addslashes( @$_GET[ 'p_name' ] );

if( !plugin_exists( $p_name ) ){
	error(
		'Plugin "' . $p_name . '" is not available. In order to install the plugin must be downloaded.',
		'Plugin Error'
	);
}

$PLUGINS[ $p_name ] = 0;

/**
 * rewrite settings file with new plugin array 
 */
settings_rewrite( $SETTINGS, $DB, $PLUGINS );

cache_clear( );

header('location: settings.php?page=plugins');
?>
