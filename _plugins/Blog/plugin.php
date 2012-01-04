<?php

/**
 * Blog Plugin, Furasta.Org
 *
 * @author	Conor Mac Aoidh <conormacaoidh@gmail.com>
 * @licence	http://furasta.org/licence.txt The BSD Licence
 * @version	1
 */

$plugin = array(
	'name' => 'Blog',
	'description' => 'Adds a blog page type which allows creation of blog posts.',
	'version' => 1,
	'href' => 'http://furasta.org',
	'email' => 'support@furasta.org',

	'admin' => array(
		'filter_lang' => 'blog_lang',
		'page_type' => array(
			'name' => 'Blog',
			'function' => 'blog_admin_page_type'
		),
		'page' => 'blog_admin',

	),

	'frontend' => array(
		'page_type' => 'blog_frontend_page_type'
	)
);

/**
 * blog_lang
 *
 * handles languages for the blog plugin
 * 
 * @params array $old_lang
 * @return array
 */
function blog_lang( $old_lang ){

	$loc = HOME . '_plugins/Blog/lang/';
	if( file_exists( $loc . LANG . '.php' ) )
		require $loc . LANG . '.php';
	else
		require $loc . 'English.php';

	return array_merge( $old_lang, $lang );
}

/**
 * blog_admin_page_type
 *
 * loads the admin page type for the blog plugin
 *
 * @params int $id
 * @return void 
 */
function blog_admin_page_type( $id ){
	require HOME . '_plugins/Blog/admin/page-type.php';
}

/**
 * blog_frontend_page_type
 *
 * load the frontend page type for the blog plugin
 * 
 * @params int $id
 * @return void
 */
function blog_frontend_page_type( $id ){
	require HOME . '_plugins/Blog/frontend/index.php';
}

/**
 * blog admin
 *
 * admin page for the blog plugin, allows creation of blog posts
 *
 * @return void
 */
function blog_admin( ){
	require HOME . '_plugins/Blog/admin/index.php';
}
?>
