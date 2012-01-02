<?php

/**
 * Forum Plugin, Furasta.Org
 *
 * @author	Conor Mac Aoidh <conormacaoidh@gmail.com>
 * @licence	http://furasta.org/licence.txt The BSD Licence
 * @version	1
 */

$plugin = array(
	'name' => 'Forum',
	'description' => 'Adds a forum page type with users, profiles and forums.',
	'version' => 1,
	'href' => 'http://furasta.org',
	'email' => 'support@furasta.org',

	'admin' => array(
		'filter_lang' => 'forum_lang',
		'filter_group_permissions' => 'forum_group_permissions',
		'page_type' => array(
			'name' => 'Forum',
			'function' => 'forum_admin_page_type'
		)
	),

	'frontend' => array(
		'page_type' => 'forum_frontend_page_type'
	)
);

/**
 * forum_lang
 *
 * handles languages for the forum plugin
 * 
 * @params array $old_lang
 * @return array
 */
function forum_lang( $old_lang ){

	$loc = HOME . '_plugins/Forum/lang/';
	if( file_exists( $loc . LANG . '.php' ) )
		require $loc . LANG . '.php';
	else
		require $loc . 'English.php';

	return array_merge( $old_lang, $lang );
}

/**
 * forum_group_permissions
 *
 * adds permissions for the forum plugin
 *
 * @params array $perms
 * @return array
 */
function forum_group_permissions( $perms ){
	$perms[ 'forum' ] = 'Can Post to Forum';
	$perms[ 'forum_moderate' ] = 'Can Moderate Forum';
	return $perms;
}

/**
 * forum_admin_page_type
 *
 * loads the admin page type for the forum plugin
 *
 * @params int $id
 * @return void 
 */
function forum_admin_page_type( $id ){
	require HOME . '_plugins/Forum/admin/index.php';
}

/**
 * forum_frontend_page_type
 *
 * load the frontend page type for the forum plugin
 * 
 * @params int $id
 * @return void
 */
function forum_frontend_page_type( $id ){
	require HOME . '_plugins/Forum/frontend/index.php';
}

?>
