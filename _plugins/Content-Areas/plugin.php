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
		'filter_menu' => 'content_areas_filter_menu'
	),

	'frontend' => array(
		'template_function' => array(
			'name' => 'content_area',
			'function' => 'content_areas_frontend_template_function'
		),
		'filter_metadata' => 'content_area_frontend_filter_metadata'
	)
);

/**
 * define plugin specific constants
 */
define( 'CONTENT_AREAS', PREFIX . 'CONTENT_AREAS' );

function content_area_frontend_filter_metadata( $metadata ){
	$url = cache_js( 'FURASTA_FRONTEND_CONTENT_AREAS', '_plugins/Content-Areas/frontend/areas.js' );
	return $metadata .= '<script type="text/javascript" src="' . $url . '"></script>';
}


function content_areas_frontend_template_function( $params, &$smarty ){
	$ContentAreas = ContentAreas::getInstance( );
	$registered = ( $ContentAreas->isRegistered( $params[ 'name' ] ) ) ? 
		'' : ' content-area-unregistered';
	echo '<div class="content-area' . $registered . '" id="content-area-' . $params[ 'name' ] . '">';
	echo $ContentAreas->getContent( $params[ 'name' ], $smarty->_tpl_vars[ 'page_id' ] );
	echo '</div>';
}

function content_areas_filter_menu( $menu, $url ){

	foreach( $menu as $item => $value ){
		if( $item == 'Settings' )
			$menu[ $item ][ 'submenu' ][ 'Content Areas' ] = array( 'url' => $url );

}	return $menu;
}
?>
