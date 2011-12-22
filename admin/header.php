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

	$menu_items=array(
		'Overview'=>array(
			'url'=>$url . 'index.php',
		),
		'Pages'=>array(
			'url'=>$url . 'pages.php',
			'submenu'=>array(
				'Edit Pages'=>array(
						'url'=>$url . 'pages.php?page=list',
				),
				'New Page'=>array(
						'url'=>$url . 'pages.php?page=new',
				),
				'Trash'=>array(
						'url'=>$url . 'pages.php?page=trash',
				),
			),
		),
		'Users & Groups'=>array(
			'url'=>$url . 'users.php',
			'submenu'=>array(
				'Edit Users'=>array(
					'url'=>$url . 'users.php?page=users',
				),
				'Edit Groups'=>array(
					'url'=>$url . 'users.php?page=groups',
				),
			),
		),
		'Settings'=>array(
			'url'=>$url . 'settings.php',
			'submenu'=>array(
				'Configuration'=>array(
					'url'=>$url . 'settings.php?page=configuration',
				),
				'Template'=>array(
						'url'=>$url . 'settings.php?page=template',
				),
				'Plugins'=>array(
						'url'=>$url . 'settings.php?page=plugins',
				),
				'Update'=>array(
						'url'=>$url . 'settings.php?page=update',
				),
			),
		),
	);

	$menu_items=$Plugins->adminMenu($menu_items);

	// filter language
	$menu = array( );
	foreach( $menu_items as $item => $val ){
		$name = $Template->e( $item );
		$new_val = $val;
		foreach( $val[ 'submenu' ] as $n => $arr ){
			$new_n = $Template->e( $n );
			$new_val[ 'submenu' ][ $new_n ] = $arr;
		}
		$menu[ $name ] = $new_val;
	}
	$menu_items = $menu;

	$menu=display_menu($menu_items);

        $menu_items_cache=json_encode($menu);
        cache($cache_file,$menu_items_cache,'USERS');
}

$Template->add('menu',$menu);

$javascript='
$(document).ready(function(){
        rowColor();
        $("#menu ul").dropDownMenu({timer:1500,parentMO:"parent-hover"});
        var link=window.location.href.split( "?" );
	var path = link[ 0 ];
        if(path=="' . SITEURL . 'admin"||path=="' . SITEURL . 'admin/"){
                $("#Overview").addClass("current");
        }
	else if( path == "' . SITEURL . 'admin/plugin.php" ){
		link = link[ 1 ].split(/[=&]+/);
		$( "#" + link[ 1 ] ).addClass( "current" );
	}
        else{
                $("#menu li a[href=\'"+path+"\']").addClass("current");
        }

	$( "#errors-close" ).live( "click", function( ){
		fetch( "' . SITEURL . '_inc/ajax.php?file=admin/settings/update/system-alert.php" );
		$( "#system-error" ).fadeOut( "fast", function( ){ $( this ).remove( ); } );
	});
});
';

$Template->loadJavascript( '_inc/js/admin.js' );
$Template->add( 'javascript', $javascript );

?>
