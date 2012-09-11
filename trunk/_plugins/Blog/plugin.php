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
	'version' => 2,
	'href' => 'http://furasta.org',
	'email' => 'support@furasta.org',

	'admin' => array(
		'filter_lang' => 'blog_lang',
		'filter_menu' => 'blog_admin_menu',
		'page_type' => array(
			'name' => 'Blog',
			'function' => 'blog_admin_page_type'
		),
		'page' => 'blog_admin',
		'overview_item' => array(
			'name' => 'Recent Blog Posts',
			'function' => 'blog_overview_item'
		)
	),

	'frontend' => array(
		'page_type' => 'blog_frontend_page_type',
		'filter_page_vars' => 'blog_filter_page_vars',
		'filter_breadcrumbs' => 'blog_filter_breadcrumbs'
	),

	'general' => array(
		'filter_htaccess' => 'blog_filter_htaccess'
	)
);

/**
 * blog_admin_menu
 *
 * adds the blog page to the admin menu
 *
 * @params array $menu
 * @params string $url
 * @return array
 */
function blog_admin_menu( $items, $url ){

	$Template = Template::getInstance( );

	$menu = array( );

	foreach( $items as $item => $val ){
		if( $item == 'menu_users_groups' ){
			$menu[ 'menu_blog' ] = array(
				'name' => $Template->e( 'menu_blog' ),
				'url' => $url,
			);
		}
		$menu[ $item ] = $val;
	}

	return $menu;
}

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

/**
 * blog_overview_item
 *
 * adds an overview item to the overview page in the
 * admin area
 *
 * @return void
 */
function blog_overview_item( ){
	require HOME . '_plugins/Blog/admin/overview-item.php';
}

/**
 * blog_filter_htaccess
 *
 * filters the htaccess_rewrite function to add rules
 * for blogs
 *
 * @params array $rules
 * @return array
 */
function blog_filter_htaccess( $old_rules ){
	$rules = array( );
	$blogs = rows( 'select id, slug from ' . DB_PAGES . ' where type="Blog"' );

	// add new rules if blogs exist
	if( count( $blogs ) != 0 ){
		foreach( $old_rules as $name => $rule ){
			foreach( $blogs as $blog ){
				if( $name == 'pages' )
					$rules[ 'blog_' . $blog[ 'id' ] ] = 'RewriteRule ^' . $blog[ 'slug' ] . '/(.*)$ /index.php?page=' . $blog[ 'slug' ] . '&blog_post=$1 [L]';
				$rules[ $name ] = $rule;
			}
		}
		return $rules;
	}

	return $old_rules;
}

/**
 * blog_filter_breadcrumbs
 *
 * adds date to breadcrumbs for blog pages
 *
 * @params array $breadcrumbs
 * @return array
 */
function blog_filter_breadcrumbs( $breadcrumbs ){

	$post = rtrim( @$_GET[ 'blog_post' ], '/' );
	if( $post != 0 ){
		if( strpos( $post, '/' ) !== -1 )
			$date = explode( '/', $post );
		else
			$date = array( $post );

		$url = end( $breadcrumbs );
		foreach( $date as $d ){
			$url .= $d . '/';
			$breadcrumbs[ $d ] = $url;
		}
	}

	return $breadcrumbs;
}

/**
 * blog_filter_page_vars
 *
 * changes the page name to the blog name if its
 * a blog page
 * 
 * @params array $Page
 * @return array
 */
function blog_filter_page_vars( $Page ){
	if( $Page[ 'type' ] == 'Blog' ){
		$options = options( 'page_' . $Page[ 'id' ] );
		if( isset( $options[ 'blog_title' ] ) )
			$Page[ 'name' ] = $options[ 'blog_title' ];
	}
	return $Page;
}
?>
