<?php

/**
 * Define File, Furasta.Org
 *
 * The define file loads all core files, and defines all
 * main constans. It also loads the /.settings.php file.
 *
 * @author     Conor Mac Aoidh <conormacaoidh@gmail.com>
 * @license    http://furasta.org/licence.txt The BSD License
 * @version    1.0
 */

/**
 * set up initial constants and default settings
 */
define( 'START_TIME', microtime( true ) );
define( 'HOME', substr( dirname( __FILE__ ), 0, -4) );
define( 'REVISION', 100 );

date_default_timezone_set( 'UTC' );

/**
 * redirect to install dir if no config found
 */
if( !file_exists( HOME . '.settings.php' ) ){
        header( 'location: install/index.php' );
	exit;
}

/**
 * require config - personal settings, db settings etc 
 */
require HOME . '.settings.php';

/**
 * add function files 
 */
$function_dir = HOME . '_inc/function/';

require $function_dir . 'system.php';
require $function_dir . 'db.php';
require $function_dir . 'plugin.php';
require $function_dir . 'cache.php';
require $function_dir . 'template.php';

/**
 * register furasta autoloader
 */
spl_autoload_register( 'furasta_autoload' );

/**
 * connect to database - display error if details are wrong 
 */
if( ! ( $connect = mysql_connect( $DB[ 'host' ], $DB[ 'user' ], $DB[ 'pass' ] ) ) ){
	error( 
		'The MySQL connection details are incorrect. The hostname, username or password are incorrect.',
		'MySQL Connection Failure'
	);
}
if( ! mysql_select_db( $DB['name'], $connect ) ){
	error(
		'Cannot connect to the MySQL database. Please make sure that the database name is correct.',
		'Database Connection Failure'
	);
}

/**
 * perform stripslashes on the $SETTINGS array 
 */
$SETTINGS = stripslashes_array( $SETTINGS );

/**
 * start session and get instance of the Plugin class 
 * cycle through active plugins and require plugin files
 */
session_start( );

/**
 * if revision is not defined or is smaller than revision
 * number in ,settings.php, run upgrade script
 */
$update = false;
if( !defined( 'SET_REVISION' ) || SET_REVISION < REVISION ){
  require HOME . '_inc/upgrade.php';
  $update = true;
}

/**
 * if no htaccess create one
 */
if( !file_exists( HOME . '.htaccess' ) )
	htaccess_rewrite( );

/**
 * get instance of plugins class, load plugin files
 * and register plugins, check if they need upgrading
 */
$Plugins = Plugins::getInstance( );

foreach( $PLUGINS as $p_name => $version ){
	require HOME . '_plugins/' . $p_name . '/plugin.php';

	/**
	 * if upgrade required, run install script
	 */
	if( $version < $plugin[ 'version' ] && file_exists( HOME . '_plugins/' . $p_name . '/install.php' ) ){
		$update = true;
		require HOME . '_plugins/' . $p_name . '/install.php';
		$PLUGINS[ $p_name ] = $plugin[ 'version' ];
	}

	/**
	 * register the $plugin array from the plugins file
	 */
	$Plugins->register( $p_name, $plugin );
}

/**
 * rewrite settings file with new plugin versions
 * if required
 */
if( $update )
	settings_rewrite( $SETTINGS, $DB, $PLUGINS );

//$Plugins->refactor( );
?>
