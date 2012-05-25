<?php

/**
 * Update File, Furasta.Org
 *
 * When an upgrade is run, this file will be automatically run
 * and it will run upgrade scripts
 *
 * @author     Conor Mac Aoidh <conormacaoidh@gmail.com>
 * @license    http://furasta.org/licence.txt The BSD License
 * @version    1.0
 */

$version = 0;
$constants = array( );

switch( VERSION ){
	// upgrades will go here
}

$constants[ 'VERSION' ] = $version;
settings_rewrite( $SETTINGS, $DB, $PLUGINS, $constants );
?>
