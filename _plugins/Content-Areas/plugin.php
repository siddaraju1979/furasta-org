<?php

/**
 * Content Areas Plugin, Furasta.Org
 *
 * @author	Conor Mac Aoidh <conormacaoidh@gmail.com>
 * @licence	http://furasta.org/licence.txt The BSD Licence
 * @version	1
 */

$plugin = array(
	'name' => 'Content Areas',
	'description' => 'Allows the use of content areas such as header, footer, sidebar.',
	'version' => 1,
	'href' => 'http://furasta.org',
	'email' => 'support@furasta.org',

	'admin' => array(
		'filter_menu' => 'content_areas_filter_menu',
		'filter_lang' => 'content_areas_lang',
		'page' => 'content_areas_admin',
		'content_area_widgets' => array(
			array(
				'name' => 'Textarea',
				'function' => 'content_areas_admin_widget_textarea'
			),
			array(
				'name' => 'Breadcrumbs',
				'function' => 'content_areas_admin_widget_breadcrumbs'
			),
			array(
				'name' => 'Menu',
				'function' => 'content_areas_admin_widget_menu'
			)
		)
	),

	'frontend' => array(
		'template_function' => array(
			'name' => 'content_area',
			'function' => 'content_areas_frontend_template_function'
		),
		'content_area_widgets' => array(
			array(
				'name' => 'Textarea',
				'function' => 'content_areas_frontend_widget_textarea'
			),
			array(
				'name' => 'Breadcrumbs',
				'function' => 'content_areas_frontend_widget_breadcrumbs'
			),
			array(
				'name' => 'Menu',
				'function' => 'content_areas_frontend_widget_menu'
			)
		)
	)
);

// add ContentAreas class
require 'ContentAreas.php';

/**
 * content_areas_frontend_template_function
 *
 * the template function for content areas, returns the
 * content for the specific content area
 *
 * @params array $params
 * @params object $smarty
 * @return void
 */
function content_areas_frontend_template_function( $params, &$smarty ){
	$ContentAreas = ContentAreas::getInstance( );
	$registered = ( $ContentAreas->isRegistered( $params[ 'name' ] ) ) ? 
		'' : ' content-area-unregistered';
	echo '<div class="content-area' . $registered . '" id="content-area-' . $params[ 'name' ] . '">';
	echo $ContentAreas->getContent( $params[ 'name' ], $smarty->_tpl_vars[ 'page_id' ] );
	echo '</div>';
}

/**
 * content_areas_filter_menu
 *
 * adds the content areas pane to the admin menu
 * 
 * @params string $menu
 * @params string $url
 * @return string
 */
function content_areas_filter_menu( $items, $url ){

	$menu = array( );

	foreach( $items as $item => $val ){
		if( $item == 'menu_users_groups' )
			$menu[ 'menu_content_areas' ] = array( 'name' => 'Content Areas', 'url' => $url );
		$menu[ $item ] = $val;
	}
		
	return $menu;
}

/**
 * content_areas_admin
 *
 * echos the admin area content areas page
 */
function content_areas_admin( ){
	require HOME . '_plugins/Content-Areas/admin/index.php';
}

function content_areas_admin_widget_textarea( ){
	$Template = Template::getInstance( );
	$Template->add( 'content', 'test' );
}

function content_areas_frontend_widget_textarea( ){
	echo 'test';
}

/**
 * content_areas_admin_widget_breadcrumbs
 *
 *
 */
function content_areas_admin_widget_breadcrumbs( ){
	$Template = Template::getInstance( );

	$content = '
		test breadcrumb options
	';

	$Template->add( 'content', $content );
}

/**
 * content_areas_frontend_widget_breadcrumbs
 *
 * calls the built in frontend_breadcrumbs function
 *
 */
function content_areas_frontend_widget_breadcrumbs( ){
	echo frontend_breadcrumbs( array( ) );
}

function content_areas_admin_widget_menu( ){
	$Template = Template::getInstance( );
	$Template->add( 'content', 'bread crumbs!' );
}

/**
 * content_areas_frontend_widget_menu
 *
 * calls the built in frontend_menu function
 *
 */
function content_areas_frontend_widget_menu( ){
	echo frontend_menu( );
}

/**
 * content_areas_lang
 *
 * managaes language features for the content areas plugin
 *
 * @params array $lang
 * @return $lang
 */
function content_areas_lang( $lang ){
	switch( LANG ){
		case 'Gaeilge':
			$new_lang = array(

			);
		break;
		default:
			$new_lang = array(
				'content_areas_drag_widgets' => 'Drag widgets here!',
				'content_areas_confirm_delete' => 'Are you sure you wish to <b>permenantly delete</b> this widget and all data associated with it?',
			);
	}
	return array_merge( $lang, $new_lang );
}
?>
