<?php

/**
 * Admin Header, Furasta.Org
 *
 * The admin header which is loaded on every page in
 * the admin area.
 *
 * @author     Conor Mac Aoidh <conormacaoidh@gmail.com>
 * @license    http://furasta.org/licence.txt The BSD License
 * @version    1.0
 */

require '../_inc/define.php';
require $function_dir . 'admin.php';

$admin_dir = HOME . 'admin/';

$User = User::getInstance( );

if( !$User->verify( ) )
        require 'login.php';

$Template = Template::getInstance( );

/**
 * set system runtime errors 
 */
if( $SETTINGS[ 'system_alert' ] != '' && $User->hasPerm( 's' ) )
	$Template->runtimeError{ 'system_alert' } = $SETTINGS[ 'system_alert' ];

/**
 * see if any errors should be triggered
 */
if( isset( $_GET[ 'error' ] ) )
	$Template->runtimeError( $_GET[ 'error' ] );

/**
 * execute the onload plugin functions 
 */
$Plugins->hook( 'admin', 'on_load' );

/**
 * check for updates 
 *
$cache_file = md5( 'FURASTA_ADMIN_RSS_UPDATE' );
if( !cache_is_good( $cache_file, '60*60*24*3', 'RSS' ) ){
	/**
	 * fetch update feed
	 *
	$rss = rss_fetch( 'http://furasta.org/update.xml', 'item' );

	/**
	 * if a new version is available log a system error
	 *
	foreach( $rss as $feed ){

		if( VERSION < $feed[ 'description' ] ){
			$SETTINGS[ 'system_alert' ] = '<span id="error-update">' . $Template->errorToString( '14', array( $feed[ 'title' ], $feed['link' ] ) ) . '</span>';
			settings_rewrite( $SETTINGS, $DB, $PLUGINS ); 
		}

	}

	cache( $cache_file, json_encode( $rss ), 'RSS' ); 
}
*/
$cache_file = md5( 'FURASTA_ADMIN_MENU_' . $_SESSION[ 'user' ][ 'id' ] );

if( cache_exists( $cache_file, 'USERS' ) && DIAGNOSTIC_MODE == 0 )
	$menu = json_decode( cache_get( $cache_file, 'USERS' ) );
else{
	$url = SITEURL . 'admin/';

	$menu_items = array(
		'menu_overview' => array(
			'url' => $url . 'index.php',
			'name' => $Template->e( 'menu_overview' )
		),
		'menu_pages' => array(
			'url' => $url . 'pages.php',
			'name' => $Template->e( 'menu_pages' ),
			'submenu' => array(
				'menu_edit_pages' => array(
					'name' => $Template->e( 'menu_edit_pages' ),
					'url' => $url . 'pages.php?page=list'
				),
				'menu_new_page' => array(
					'name' => $Template->e( 'menu_new_page' ),
					'url' => $url . 'pages.php?page=new'
				),
				'menu_trash' => array(
					'name' => $Template->e( 'menu_trash' ),
					'url' => $url . 'pages.php?page=trash'
				),
			),
		),
		'menu_users_groups' => array(
			'name' => $Template->e( 'menu_users_groups' ),
			'url' => $url . 'users.php',
			'submenu' => array(
				'menu_edit_users' => array(
					'name' => $Template->e( 'menu_edit_users' ),
					'url' => $url . 'users.php?page=users'
				),
				'menu_edit_groups' => array(
					'name' => $Template->e( 'menu_edit_groups' ),
					'url' => $url . 'users.php?page=groups'
				),
			),
		),
		'menu_settings' => array(
			'name' => $Template->e( 'menu_settings' ),
			'url' => $url . 'settings.php',
			'submenu' => array(
				'menu_configuration' => array(
					'name' => $Template->e( 'menu_configuration' ),
					'url' => $url . 'settings.php?page=configuration',
				),
				'menu_template' => array(
					'name' => $Template->e( 'menu_template' ),
					'url' => $url . 'settings.php?page=template',
				),
				'menu_plugins' => array(
					'name' => $Template->e( 'menu_plugins' ),
					'url' => $url . 'settings.php?page=plugins'
				),
				'menu_update'=>array(
					'name' => $Template->e( 'menu_update' ),
					'url' => $url . 'settings.php?page=update'
				),
			),
		),
	);

	$menu_items = $Plugins->adminMenu( $menu_items );

	$menu = display_menu( $menu_items );

        $menu_items_cache = json_encode( $menu );
        cache( $cache_file, $menu_items_cache, 'USERS' );
}

$Template->add('menu',$menu);

$javascript='
window.furasta = {
	site : {
		url : "' . SITEURL . '"
	},
	lang : ' . json_encode( $Template->lang ) . ',
	lang_errors : ' . json_encode( $Template->lang_errors ) . '
};
';

$Template->loadJavascript( '_inc/js/admin.js' );
$Template->loadJavascript( 'FURASTA_ADMIN_HEADER_SCRIPT', $javascript );

?>
