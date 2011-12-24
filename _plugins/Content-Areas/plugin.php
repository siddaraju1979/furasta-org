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
		'page' => 'content_areas_admin',
		'template_override' => '_plugins/Content-Areas/admin/template.php',
		'content_area_widgets' => array(
			array(
				'name' => 'Content',
				'function' => 'content_areas_admin_widget_content'
			)
		)
	),

	'frontend' => array(
		'template_function' => array(
			'name' => 'content_area',
			'function' => 'content_areas_frontend_template_function'
		),
		'filter_metadata' => 'content_areas_frontend_filter_metadata',
		'content_area_widgets' => array(
			array(
				'name' => 'Content',
				'function' => 'content_areas_frontend_widget_content'
			)
		)
	)
);

// add ContentAreas class
require 'ContentAreas.php';

/**
 * content_areas_frontent_filter_metadata
 *
 * adds the content areas javascript file to the frontend
 *
 * @params string $metadata
 * @return string
 */
function content_areas_frontend_filter_metadata( $metadata ){
	$url = cache_js( 'FURASTA_FRONTEND_CONTENT_AREAS', '_plugins/Content-Areas/frontend/areas.js' );
	return $metadata .= '<script type="text/javascript" src="' . $url . '"></script>';
}

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
function content_areas_filter_menu( $menu, $url ){

	$menu[ 'menu_content_areas' ] = array( 'name' => 'Content Areas', 'url' => $url );

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

function content_areas_admin_widget_content( ){
	$Template = Template::getInstance( );
	$Template->add( 'content', 'test' );
}

function content_areas_frontend_widget_content( ){
	echo 'test';
}
?>
