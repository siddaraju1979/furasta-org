<?php

/**
 * Backup Plugin, Furasta.Org
 *
 * @author	Conor Mac Aoidh <conormacaoidh@gmail.com>
 * @licence	http://furasta.org/licence.txt The BSD Licence
 * @version	1
 */

$plugin = array(
	'name' => 'Backup',
	'description' => 'Can create backups and also restore from saved backups',
	'version' => 1,
	'href' => 'http://furasta.org',
	'email' => 'support@furasta.org',

	'admin' => array(
		'filter_menu' => 'backup_filter_menu',
		'page' => 'backup_admin'
	)
);

/**
 * backup_filter_menu
 *
 * adds the backup page to the admin menu
 * 
 * @params string $menu
 * @params string $url
 * @return string
 */
function backup_filter_menu( $items, $url ){

	$menu = array( );

	foreach( $items as $item => $val ){
		if( $item == 'menu_users_groups' )
			$menu[ 'menu_backup' ] = array( 'name' => 'Backup', 'url' => $url );
		$menu[ $item ] = $val;
	}
		
	return $menu;
}

/**
 * backup_admin
 *
 * echos the admin area backup page
 */
function backup_admin( ){
	require HOME . '_plugins/Backup/admin/index.php';
}
?>
